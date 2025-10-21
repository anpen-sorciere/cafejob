@echo off
setlocal enabledelayedexpansion

echo ========================================
echo カフェJob デプロイ用ファイルコピー
echo ========================================
echo.

:: ソースディレクトリとデプロイディレクトリの設定
set SOURCE_DIR=C:\xampp\htdocs\cafejob
set DEPLOY_DIR=C:\develop\cafejob

:: ディレクトリの存在確認
if not exist "%SOURCE_DIR%" (
    echo エラー: ソースディレクトリが見つかりません: %SOURCE_DIR%
    pause
    exit /b 1
)

:: デプロイディレクトリが存在しない場合は作成
if not exist "%DEPLOY_DIR%" (
    echo デプロイディレクトリを作成中: %DEPLOY_DIR%
    mkdir "%DEPLOY_DIR%"
)

echo ソースディレクトリ: %SOURCE_DIR%
echo デプロイディレクトリ: %DEPLOY_DIR%
echo.

:: コピー対象ファイルの拡張子
set EXTENSIONS=*.php *.html *.css *.js *.sql *.md *.txt *.bat *.json *.xml *.yml *.yaml

:: コピー対象ディレクトリ（除外するディレクトリも指定）
set EXCLUDE_DIRS=logs uploads .git .vscode node_modules

echo ファイルコピーを開始します...
echo.

:: カウンター初期化
set /a copied_files=0
set /a skipped_files=0
set /a total_files=0

:: ルートディレクトリのファイルをコピー
echo [ルートディレクトリ]
for %%e in (%EXTENSIONS%) do (
    for %%f in ("%SOURCE_DIR%\%%e") do (
        if exist "%%f" (
            set /a total_files+=1
            set source_file=%%f
            set filename=%%~nxf
            set deploy_file=%DEPLOY_DIR%\!filename!
            
            :: ファイルが存在しない、または更新日時が新しい場合のみコピー
            if not exist "!deploy_file!" (
                echo   新規: !filename!
                copy "!source_file!" "!deploy_file!" >nul
                set /a copied_files+=1
            ) else (
                :: ファイルの更新日時を比較
                for %%s in ("!source_file!") do set source_time=%%~ts
                for %%d in ("!deploy_file!") do set deploy_time=%%~td
                
                :: 更新日時が新しい場合はコピー
                if "!source_time!" gtr "!deploy_time!" (
                    echo   更新: !filename!
                    copy "!source_file!" "!deploy_file!" >nul
                    set /a copied_files+=1
                ) else (
                    echo   スキップ: !filename!
                    set /a skipped_files+=1
                )
            )
        )
    )
)

:: サブディレクトリを再帰的に処理
echo.
echo [サブディレクトリ]
for /d %%d in ("%SOURCE_DIR%\*") do (
    set dirname=%%~nd
    set skip_dir=false
    
    :: 除外ディレクトリかチェック
    for %%e in (%EXCLUDE_DIRS%) do (
        if "!dirname!"=="%%e" set skip_dir=true
    )
    
    if not !skip_dir!==true (
        echo ディレクトリ: !dirname!
        
        :: デプロイ先ディレクトリを作成
        if not exist "%DEPLOY_DIR%\!dirname!" (
            mkdir "%DEPLOY_DIR%\!dirname!"
        )
        
        :: ディレクトリ内のファイルをコピー
        for %%e in (%EXTENSIONS%) do (
            for %%f in ("%%d\%%e") do (
                if exist "%%f" (
                    set /a total_files+=1
                    set source_file=%%f
                    set filename=%%~nxf
                    set deploy_file=%DEPLOY_DIR%\!dirname!\!filename!
                    
                    :: ファイルが存在しない、または更新日時が新しい場合のみコピー
                    if not exist "!deploy_file!" (
                        echo   新規: !dirname!\!filename!
                        copy "!source_file!" "!deploy_file!" >nul
                        set /a copied_files+=1
                    ) else (
                        :: ファイルの更新日時を比較
                        for %%s in ("!source_file!") do set source_time=%%~ts
                        for %%d in ("!deploy_file!") do set deploy_time=%%~td
                        
                        :: 更新日時が新しい場合はコピー
                        if "!source_time!" gtr "!deploy_time!" (
                            echo   更新: !dirname!\!filename!
                            copy "!source_file!" "!deploy_file!" >nul
                            set /a copied_files+=1
                        ) else (
                            echo   スキップ: !dirname!\!filename!
                            set /a skipped_files+=1
                        )
                    )
                )
            )
        )
        
        :: サブディレクトリ内のサブディレクトリも処理
        for /d %%sd in ("%%d\*") do (
            set subdirname=%%~nsd
            set subdirpath=!dirname!\!subdirname!
            
            :: デプロイ先サブディレクトリを作成
            if not exist "%DEPLOY_DIR%\!subdirpath!" (
                mkdir "%DEPLOY_DIR%\!subdirpath!"
            )
            
            echo サブディレクトリ: !subdirpath!
            
            :: サブディレクトリ内のファイルをコピー
            for %%e in (%EXTENSIONS%) do (
                for %%f in ("%%sd\%%e") do (
                    if exist "%%f" (
                        set /a total_files+=1
                        set source_file=%%f
                        set filename=%%~nxf
                        set deploy_file=%DEPLOY_DIR%\!subdirpath!\!filename!
                        
                        :: ファイルが存在しない、または更新日時が新しい場合のみコピー
                        if not exist "!deploy_file!" (
                            echo   新規: !subdirpath!\!filename!
                            copy "!source_file!" "!deploy_file!" >nul
                            set /a copied_files+=1
                        ) else (
                            :: ファイルの更新日時を比較
                            for %%s in ("!source_file!") do set source_time=%%~ts
                            for %%d in ("!deploy_file!") do set deploy_time=%%~td
                            
                            :: 更新日時が新しい場合はコピー
                            if "!source_time!" gtr "!deploy_time!" (
                                echo   更新: !subdirpath!\!filename!
                                copy "!source_file!" "!deploy_file!" >nul
                                set /a copied_files+=1
                            ) else (
                                echo   スキップ: !subdirpath!\!filename!
                                set /a skipped_files+=1
                            )
                        )
                    )
                )
            )
        )
    )
)

:: 結果表示
echo.
echo ========================================
echo コピー完了
echo ========================================
echo 総ファイル数: %total_files%
echo コピーしたファイル: %copied_files%
echo スキップしたファイル: %skipped_files%
echo.

:: デプロイディレクトリの内容確認
echo デプロイディレクトリの内容:
dir "%DEPLOY_DIR%" /b
echo.

:: 次のステップの案内
echo ========================================
echo 次のステップ:
echo 1. C:\develop\cafejob からFTPでアップロード
echo 2. Gitにコミット・プッシュ
echo ========================================
echo.

pause
