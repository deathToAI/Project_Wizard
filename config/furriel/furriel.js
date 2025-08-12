document.addEventListener('DOMContentLoaded', function() {
    // Função do relógio
    function atualizarRelogio() {
        const agora = new Date();
        let horas = agora.getHours();
        let minutos = agora.getMinutes();
        let segundos = agora.getSeconds();

        horas = horas < 10 ? '0' + horas : horas;
        minutos = minutos < 10 ? '0' + minutos : minutos;
        segundos = segundos < 10 ? '0' + segundos : segundos;

        const horaFormatada = horas + ':' + minutos + ':' + segundos;
        document.getElementById('relogio').innerHTML = horaFormatada;
    }

    // Inicia o relógio
    atualizarRelogio();
    setInterval(atualizarRelogio, 1000);

    // Função de atualização da data
    function updateDateHeader(dateValue) {
        var formatter = new Intl.DateTimeFormat('pt-BR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        var dateParts = dateValue.split('-');
        var date = new Date(
            parseInt(dateParts[0]),
            parseInt(dateParts[1]) - 1,
            parseInt(dateParts[2])
        );
        // Pegar data do BD
        console.log(`Fetching ${dateValue}`);
        fetch(`retrieve.php?date=${dateValue}`)
        //Ler o campo 'data-user-id' da linha 

        //Recuperar informação daquele usuário para a data selecionada
        // Marcar as checkbox de acordo com jsonData[$i].refeicao
        .then(response => response.text())
        // 
        .then(data => {
            const jsonData = JSON.parse(data);
            jsonData.forEach(e => {
                console.log(jsonData[0].refeicao);
            })
            const tabela = document.getElementById('tabela') // Define a tabela
            const tam_tabela = tabela.rows.length; // Define as colunas da tabela
            for (let i = 1; i < tam_tabela; i++) { //Itera pela tabela 
                var cell = tabela.rows.item(i).cells; // Verifica quantas celulas tem em cada linha
                var tam_cell = cell.length; 
                for (let j = 0; j < tam_cell; j++) { // Itera pela quantidade de células
                    // Verifica 
                    if (cell.item(j).getAttribute('data-user-id') == jsonData[e].user_id) {
                        cell.item(j).setAttribute('data-refeicao', jsonData[e].refeicao);
                    }
                }
                
            }
            document.getElementById('resposta_bd').innerHTML =  `Arranchados para: ${jsonData[0].refeicao}  <br> User_ID: ${jsonData[0].user_id} <br> ${data}`;
        });
        
        document.getElementById('diaselecionado').innerHTML = 'Data: ' + formatter.format(date);
    }

    // Inicialização
    var selectElement = document.getElementById('dia');
    if(selectElement) {
        updateDateHeader(selectElement.value);
        
        // Adiciona o event listener para mudanças
        selectElement.addEventListener('change', function() {
            updateDateHeader(this.value);
        });
    }
});
