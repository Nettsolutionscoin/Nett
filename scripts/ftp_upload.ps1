Param(
    [string]$FtpHost = 'ftpupload.net',
    [int]$Port = 21,
    [string]$User = 'if0_40708403',
    [string]$Pass = 'eiL7I99TyWVFU',
    [string]$LocalRoot = (Get-Location).Path,
    [string]$RemoteRoot = '/htdocs'
)

function Write-Log { param($m) Write-Host "[ftp] $m" }

# Helpers
function New-FtpRequest {
    param($uri, $method)
    $r = [System.Net.FtpWebRequest]::Create($uri)
    $r.Credentials = New-Object System.Net.NetworkCredential($User, $Pass)
    $r.Method = $method
    $r.UseBinary = $true
    $r.KeepAlive = $false
    return $r
}

# Timestamp for backup name
$ts = Get-Date -Format "yyyyMMdd_HHmmss"
$backupName = "backup_$ts"

# 1) Rename existing /htdocs to /backup_ts (if exists)
try {
    Write-Log "Attempting to rename remote '$RemoteRoot' to '/$backupName'..."
    # Remove leading slash for the FTP path in the request uri when working from root
    $rootName = [System.IO.Path]::GetFileName($RemoteRoot.TrimEnd('/'))
    $uri = "ftp://$FtpHost/$rootName"
    $req = New-FtpRequest $uri ([System.Net.WebRequestMethods+Ftp]::Rename)
    $req.RenameTo = $backupName
    $res = $req.GetResponse()
    $res.Close()
    Write-Log "Remote '$RemoteRoot' renamed to '/$backupName'"
}
catch {
    Write-Log "Rename failed or remote path does not exist (continuing): $_"
}

# 2) Create new /htdocs
try {
    Write-Log "Creating new remote '$RemoteRoot'..."
    $uriCreate = "ftp://$FtpHost$RemoteRoot"
    $req = New-FtpRequest $uriCreate ([System.Net.WebRequestMethods+Ftp]::MakeDirectory)
    $res = $req.GetResponse()
    $res.Close()
    Write-Log "Created $RemoteRoot"
}
catch {
    Write-Log "MakeDirectory failed (maybe already exists): $_"
}

# 3) Upload files recursively
$skip = @('.git', '.htaccess', '.gitignore')
$local = Get-Item -Path $LocalRoot
Write-Log "Uploading files from $LocalRoot to ftp://$FtpHost$RemoteRoot ..."

function Ensure-RemoteDir {
    param($relativePath)
    $parts = $relativePath -split '/'
    $path = ""
    foreach ($p in $parts) {
        if ([string]::IsNullOrEmpty($p)) { continue }
        $path = "$path/$p"
        $uriDir = "ftp://$FtpHost$RemoteRoot$path"
        try {
            $req = New-FtpRequest $uriDir ([System.Net.WebRequestMethods+Ftp]::MakeDirectory)
            $res = $req.GetResponse()
            $res.Close()
            Write-Log "Created remote dir: $path"
        }
        catch {
            # directory might already exist
        }
    }
}

function Upload-File {
    param($localFile, $remoteFilePath)
    $uri = "ftp://$FtpHost$remoteFilePath"
}

# Actually upload files
$items = Get-ChildItem -Path $LocalRoot -Recurse -File
foreach ($f in $items) {
    $rel = $f.FullName.Substring($LocalRoot.Length).TrimStart('\', '/') -replace '\\', '/'
    # Skip top-level .git and .htaccess
    $top = $rel.Split('/')[0]
    if ($skip -contains $top) { continue }
    if ($top -eq '') { $rel = $f.Name }
    if ($rel -eq 'vendor' -or $rel.StartsWith('vendor/')) { }
    # Create remote directories if needed
    $dir = [System.IO.Path]::GetDirectoryName($rel) -replace '\\', '/'
    if ($dir -ne '') { Ensure-RemoteDir $dir }
    $remotePath = "$RemoteRoot/$rel" -replace '^/', '/'
    $uri = "ftp://$FtpHost$remotePath"
    try {
        Write-Log "Uploading $rel ..."
        $req = [System.Net.FtpWebRequest]::Create($uri)
        $req.Credentials = New-Object System.Net.NetworkCredential($User, $Pass)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $req.UseBinary = $true
        $bytes = [System.IO.File]::ReadAllBytes($f.FullName)
        $req.ContentLength = $bytes.Length
        $rs = $req.GetRequestStream()
        $rs.Write($bytes, 0, $bytes.Length)
        $rs.Close()
        $res = $req.GetResponse()
        $res.Close()
    }
    catch {
        Write-Log "Upload failed for ${rel}: $_"
    }
}

# 4) Ensure contactform/mail.log exists and is writable
try {
    $remoteMailLog = "ftp://$FtpHost$RemoteRoot/contactform/mail.log"
    Write-Log "Creating remote contactform/mail.log"
    $req = New-FtpRequest $remoteMailLog ([System.Net.WebRequestMethods+Ftp]::UploadFile)
    $req.ContentLength = 0
    $rs = $req.GetRequestStream()
    $rs.Close()
    $res = $req.GetResponse()
    $res.Close()
    Write-Log "Created contactform/mail.log"
}
catch {
    Write-Log "Failed to create mail.log: $_"
}

Write-Log "Upload complete. Please extract any zip on server if you prefer, and verify site at http://nett.infinityfree.me"
