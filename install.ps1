# Installation Recettes Zéro Gaspi pour Windows

Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Installation Recettes Zéro Gaspi     ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

$dockerInstalled = Get-Command docker -ErrorAction SilentlyContinue
if (-not $dockerInstalled) {
    Write-Host "❌ Docker n'est pas installé. Veuillez installer Docker Desktop." -ForegroundColor Red
    exit 1
}

try {
    docker info | Out-Null
    Write-Host "✅ Docker est installé et en cours d'exécution" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker n'est pas démarré. Veuillez démarrer Docker Desktop." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "📦 Démarrage des containers Docker..." -ForegroundColor Yellow
& .\vendor\bin\sail up -d

Write-Host ""
Write-Host "⏳ Attente du démarrage de MySQL..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

Write-Host ""
Write-Host "🗄️  Exécution des migrations..." -ForegroundColor Yellow
& .\vendor\bin\sail artisan migrate --seed

Write-Host ""
Write-Host "🔨 Compilation des assets..." -ForegroundColor Yellow
& .\vendor\bin\sail npm run build

Write-Host ""
Write-Host "✅ Installation terminée !" -ForegroundColor Green
Write-Host ""
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  L'application est prête !             ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""
Write-Host "🌐 Application      : http://localhost" -ForegroundColor White
Write-Host "📧 Mailpit (emails) : http://localhost:8025" -ForegroundColor White
Write-Host "🔍 Meilisearch     : http://localhost:7700" -ForegroundColor White
Write-Host "📦 MinIO (storage) : http://localhost:8900" -ForegroundColor White
Write-Host ""
Write-Host "Pour arrêter : .\vendor\bin\sail down" -ForegroundColor Gray
Write-Host "Pour voir les logs : .\vendor\bin\sail logs" -ForegroundColor Gray
Write-Host ""
