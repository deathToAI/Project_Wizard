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
        .then(response => response.text())
        // 
        .then(data => {
        try {
            const jsonData = JSON.parse(data);
            console.log(jsonData);

            document.getElementById('resposta_bd').innerHTML =
            `Arranchados para: ${jsonData[0]?.refeicao ?? '-'}  <br> User_ID: ${jsonData[0]?.user_id ?? '-'} <br> ${data}`;

            const tabela = document.getElementById('tabela');
            const tam_tabela = tabela.rows.length;

            for (let i = 2; i < tam_tabela; i++) { // duas linhas de cabeçalho
            const linha = tabela.rows.item(i);
            const id = linha.getAttribute('data-id');
            console.log(`ID da linha: ${id}`);

            jsonData.forEach(element => {
                console.log(element.user_id, element.refeicao);
                if (String(element.user_id) === String(id)) {
                const refeicao = element.refeicao;
                const checkbox = linha.querySelector(`.ck[data-refeicao="${refeicao}"]`);
                if (checkbox) checkbox.checked = true;
                }
            });
            }
        } catch (error) {
            console.error('Error parsing JSON data:', error);
        }
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
