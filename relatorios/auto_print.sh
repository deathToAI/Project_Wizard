#!/bin/bash

#Define datas
DATA=$(date +%Y-%mm-%d)
DIA=$(date +%d)
MES=$(date +%m)
ANO=$(date +%Y)

dir=$(pwd)
currentMonth="$MES-$ANO"
sheetFile="Arranchamento-$DIA-$MES-$ANO.xlsx"
pdfFile="Arranchamento-$DIA-$MES-$ANO.pdf"
#Chama o script

/usr/bin/php /home/dtai/Projects/Project_Wizard/relatorios/auto_print_report.php
#Espera fechar e salvar
echo "Aguarde"
sleep 5
##Transforma em pdf o relatório
echo "Convertendo para pdf..."
soffice --convert-to pdf  /var/www/html/relatorios/$currentMonth/$sheetFile --outdir /var/www/html/relatorios/$currentMonth

##Imprime o arquivo pdf
#echo "Imprimindo /var/www/html/relatorios/$currentMonth/$pdfFile"
##lp -d "/var/www/html/relatorios/$currentMonth/$pdfFile"