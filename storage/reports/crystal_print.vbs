' crystal_print.vbs
' This VBScript exports temporary (TP) or monthly (MP) permits from Crystal Reports to PDF.

If WScript.Arguments.Count < 3 Then
    WScript.Echo "ERROR: Missing arguments. Usage: cscript.exe crystal_print.vbs <permitType> <permitIds> <pdfPath>"
    WScript.Quit 1
End If

Dim permitType, permitIdsInput, pdfPath, reportPath, tablePrefix, tempId, monthlyId
permitType = UCase(Trim(WScript.Arguments(0)))
permitIdsInput = WScript.Arguments(1)
pdfPath = WScript.Arguments(2)

If WScript.Arguments.Count >= 5 Then
    tempId = WScript.Arguments(3)
    monthlyId = WScript.Arguments(4)
Else
    tempId = 1
    monthlyId = 1
End If

' Select the appropriate report template and table prefix based on permit type
If permitType = "TP" Then
    reportPath = "d:\New-GllePermitSystem\port-entry-permit\port-entry-permit\storage\reports\temporary_permit.rpt"
    tablePrefix = "temporary_permits"
ElseIf permitType = "MP" Then
    reportPath = "d:\New-GllePermitSystem\port-entry-permit\port-entry-permit\storage\reports\monthly_permit.rpt"
    tablePrefix = "monthly_permits"
ElseIf permitType = "VH" Then
    reportPath = "d:\New-GllePermitSystem\port-entry-permit\port-entry-permit\storage\reports\vehicle_permit.rpt"
    tablePrefix = "vehicle_permits"
ElseIf permitType = "DR" Then
    reportPath = "d:\New-GllePermitSystem\port-entry-permit\port-entry-permit\storage\reports\daily_revenue.rpt"
    tablePrefix = "payments"
ElseIf permitType = "RS" Then
    reportPath = "d:\New-GllePermitSystem\port-entry-permit\port-entry-permit\storage\reports\revenue_summary.rpt"
    tablePrefix = "payments"
Else
    WScript.Echo "ERROR: Invalid permit type: " & permitType
    WScript.Quit 1
End If

On Error Resume Next

Dim crapp
Set crapp = CreateObject("CrystalRuntime.Application.11")
If Err.Number <> 0 Then
    WScript.Echo "ERROR creating app: " & Err.Description
    WScript.Quit 1
End If

Dim creport
Set creport = crapp.OpenReport(reportPath, 1)
If Err.Number <> 0 Then
    WScript.Echo "ERROR opening report: " & Err.Description
    WScript.Quit 1
End If

' Discard saved data to force fresh connection and query execution
creport.DiscardSavedData

' Set login credentials for each table
Dim table
For Each table In creport.Database.Tables
    table.SetLogOnInfo "GallePortPermits", "port_entry_permit", "root", ""
Next

' Set date parameter values if permitType is DR or RS
If permitType = "DR" Then
    Dim p
    For Each p In creport.ParameterFields
        If LCase(p.Name) = "{?enter date}" Then
            p.AddCurrentValue CDate(permitIdsInput)
        End If
    Next
ElseIf permitType = "RS" Then
    Dim datesArray, startDate, endDate, pObj
    datesArray = Split(permitIdsInput, ",")
    startDate = Trim(datesArray(0))
    endDate = Trim(datesArray(1))
    For Each pObj In creport.ParameterFields
        If LCase(pObj.Name) = "{?first date}" Then
            pObj.AddCurrentValue CDate(startDate)
        ElseIf LCase(pObj.Name) = "{?second date}" Then
            pObj.AddCurrentValue CDate(endDate)
        End If
    Next
End If

' Parse permit IDs (supports comma-separated values for batch printing)
Dim idArray, formula, i
idArray = Split(permitIdsInput, ",")

If permitType = "DR" Then
    formula = "{TPINVOICE.status} = ""Paid"" And DateValue({TPINVOICE.payment_date}) = Date(" & Replace(permitIdsInput, "-", ", ") & ")"
ElseIf permitType = "RS" Then
    Dim datesArr, sDate, eDate
    datesArr = Split(permitIdsInput, ",")
    sDate = Trim(datesArr(0))
    eDate = Trim(datesArr(1))
    formula = "{TPINVOICE.status} = ""Paid"" And DateValue({TPINVOICE.payment_date}) >= Date(" & Replace(sDate, "-", ", ") & ") And DateValue({TPINVOICE.payment_date}) <= Date(" & Replace(eDate, "-", ", ") & ")"
Else
    If UBound(idArray) = 0 Then
        ' Single permit filter
        formula = "{" & tablePrefix & ".permit_id} = """ & Trim(idArray(0)) & """"
    Else
        ' Batch permits filter using IN operator
        formula = "{" & tablePrefix & ".permit_id} in ["
        For i = 0 To UBound(idArray)
            formula = formula & """" & Trim(idArray(i)) & """"
            If i < UBound(idArray) Then
                formula = formula & ", "
            End If
        Next
        formula = formula & "]"
    End If
End If

If permitType = "VH" Then
    formula = formula & " And {temporary_permits.id} = " & tempId & " And {monthly_permits.id} = " & monthlyId
End If

' Set formula syntax and selection formula
creport.FormulaSyntax = 0 ' crCrystalSyntaxFormula
creport.RecordSelectionFormula = formula

If Err.Number <> 0 Then
    WScript.Echo "ERROR setting selection formula: " & Err.Description
    WScript.Quit 1
End If

' Configure Export to PDF
creport.ExportOptions.FormatType = 31 ' PDF
creport.ExportOptions.DestinationType = 1 ' DiskFile
creport.ExportOptions.DiskFileName = pdfPath
creport.Export False

If Err.Number <> 0 Then
    WScript.Echo "ERROR exporting report: " & Err.Description
    WScript.Quit 1
Else
    WScript.Echo "SUCCESS"
    WScript.Quit 0
End If
