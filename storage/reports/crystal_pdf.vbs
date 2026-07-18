' crystal_pdf.vbs
Dim permitId
permitId = "TP26060001"
Dim reportPath
reportPath = "d:\New-GllePermitSystem\port-entry-permit\port-entry-permit\storage\reports\temporary_permit.rpt"
Dim pdfPath
pdfPath = "d:\New-GllePermitSystem\port-entry-permit\port-entry-permit\storage\reports\test.pdf"

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

' Set login credentials for each table
Dim table
For Each table In creport.Database.Tables
    table.SetLogOnInfo "GallePortPermits", "port_entry_permit", "root", ""
Next

creport.FormulaSyntax = 0
creport.RecordSelectionFormula = "{temporary_permits.permit_id} = """ & permitId & """"

' Export to PDF
' 31 corresponds to crEFTPortableDocFormat (PDF)
' 1 corresponds to crETDiskFile
creport.ExportOptions.FormatType = 31
creport.ExportOptions.DestinationType = 1
creport.ExportOptions.DiskFileName = pdfPath
creport.Export False

If Err.Number <> 0 Then
    WScript.Echo "ERROR during export: " & Err.Description
    WScript.Quit 1
Else
    WScript.Echo "SUCCESS"
End If
