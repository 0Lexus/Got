
@echo off
REM ============================================================
REM Projeto: Got
REM Arquivo: rodar_bot.bat
REM Funcao: Executar o bot PHP em ciclos de 10 minutos.
REM Ambiente: Windows + XAMPP
REM ============================================================

REM Define o titulo da janela do CMD.
title Got - Supervisor do Bot

REM Entra na pasta onde esta o script PHP.
REM O parametro /d permite trocar tambem a unidade do disco.
cd /d "C:\xampp\htdocs\Got\bot"

REM Verifica se a mudanca de diretorio funcionou.
REM Se falhar, encerra o script para evitar executar na pasta errada.
if errorlevel 1 (
    echo ERRO: Nao foi possivel acessar a pasta do bot.
    pause
    exit /b 1
)

REM Cria um ponto de retorno para repetir o ciclo.
:INICIO

REM Registra no arquivo de log o inicio de uma execucao.
REM >> acrescenta o texto ao final do arquivo, sem apagar o historico.
echo [%date% %time%] INICIO >> "C:\xampp\htdocs\Got\bot\bot.log"

REM Executa o bot usando o interpretador PHP do XAMPP.
REM A saida normal e os erros do PHP sao enviados ao arquivo de log.
REM 2>&1 redireciona os erros para o mesmo destino da saida normal.
C:\xampp\php\php.exe bot_pesquisa.php >> "C:\xampp\htdocs\Got\bot\bot.log" 2>&1

REM Guarda o codigo de saida do PHP imediatamente apos sua execucao.
REM Normalmente, 0 indica sucesso e valores diferentes de zero
REM indicam falha. O codigo nao identifica sozinho a causa do erro.
set "CODIGO=%errorlevel%"

REM Registra o termino da execucao e seu codigo de saida.
echo [%date% %time%] FIM - Codigo: %CODIGO% >> "C:\xampp\htdocs\Got\bot\bot.log"

REM Aguarda 600 segundos (10 minutos) antes do proximo ciclo.
REM /nobreak impede que uma tecla interrompa a contagem.
REM >nul oculta a contagem regressiva no terminal.
timeout /t 600 /nobreak >nul

REM Retorna ao inicio e executa o bot novamente.
goto INICIO
