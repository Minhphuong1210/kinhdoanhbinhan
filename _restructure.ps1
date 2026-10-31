# Script tái cấu trúc: đưa Controllers/Admin và Views/Admin vào từng module

$modulesPath = "app\Modules"
$modules = Get-ChildItem -Path $modulesPath -Directory

foreach ($module in $modules) {
    $moduleName = $module.Name
    $modulePath = $module.FullName

    Write-Host "=== Xử lý module: $moduleName ===" -ForegroundColor Cyan

    # ---- 1. Di chuyển Controllers ----
    $ctrlPath      = Join-Path $modulePath "Controllers"
    $ctrlAdminPath = Join-Path $ctrlPath "Admin"

    if (Test-Path $ctrlPath) {
        # Tạo thư mục Admin bên trong Controllers
        New-Item -ItemType Directory -Force -Path $ctrlAdminPath | Out-Null

        # Di chuyển tất cả PHP files (không phải thư mục) vào Admin/
        Get-ChildItem -Path $ctrlPath -Filter "*.php" | ForEach-Object {
            Move-Item -Path $_.FullName -Destination (Join-Path $ctrlAdminPath $_.Name) -Force
            Write-Host "  Moved controller: $($_.Name)"
        }
    }

    # ---- 2. Di chuyển Views ----
    $viewsPath      = Join-Path $modulePath "Views"
    $viewsAdminPath = Join-Path $viewsPath "Admin"

    if (Test-Path $viewsPath) {
        New-Item -ItemType Directory -Force -Path $viewsAdminPath | Out-Null

        # Di chuyển tất cả items trực tiếp trong Views/ (files và subdirs) vào Admin/
        Get-ChildItem -Path $viewsPath | Where-Object { $_.Name -ne "Admin" } | ForEach-Object {
            Move-Item -Path $_.FullName -Destination (Join-Path $viewsAdminPath $_.Name) -Force
            Write-Host "  Moved view: $($_.Name)"
        }
    }

    # ---- 3. Di chuyển routes.php → routes/admin.php ----
    $routesFile    = Join-Path $modulePath "routes.php"
    $routesDirPath = Join-Path $modulePath "routes"
    $routesAdmin   = Join-Path $routesDirPath "admin.php"

    if (Test-Path $routesFile) {
        New-Item -ItemType Directory -Force -Path $routesDirPath | Out-Null
        Move-Item -Path $routesFile -Destination $routesAdmin -Force
        Write-Host "  Moved routes.php -> routes/admin.php"
    }
}

Write-Host "`n✅ Hoàn tất di chuyển files!" -ForegroundColor Green
