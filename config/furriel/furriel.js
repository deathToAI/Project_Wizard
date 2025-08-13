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
        console.log(`Fetching ${dateValue}`); // Depuração para ver se a data correta está sendo devolvida por retrieve.php
        fetch(`retrieve.php?date=${dateValue}`)
        .then(response => response.text())
        // 
        .then(data => {
        try {
            const jsonData = JSON.parse(data); // Le a resposta em JSON
            //console.log(jsonData); //Verificar se o JSON foi lido

            document.getElementById('resposta_bd').innerHTML =
            `Arranchados para: ${jsonData[0]?.refeicao ?? '-'}  <br> User_ID: ${jsonData[0]?.user_id ?? '-'} <br> ${data}`;

            const tabela = document.getElementById('tabela'); // Obtém a tabela com o ID "tabela" do documento HTML
            const tam_tabela = tabela.rows.length; // Obtém o número de linhas da tabela

            for (let i = 2; i < tam_tabela; i++) { // Itera pelas linhas da tabela, começando da terceira linha (índice 2)
            const linha = tabela.rows.item(i);// Obtém as células da linha atual
            const id = linha.getAttribute('data-id');// Obtém o ID da linha atual
            console.log(`ID da linha: ${id}`);// Mostra o ID da linha no console(depuração)

            jsonData.forEach(element => { // Itera pelos elementos do array "jsonData"
                console.log(element.user_id, element.refeicao); // Mostra o ID do usuário e o tipo de refeição no console
                if (String(element.user_id) === String(id)) {    // Verifica se o ID do usuário corresponde ao ID da linha atual
                const refeicao = element.refeicao;  // Obtém o tipo de refeição do usuário
                const checkbox = linha.querySelector(`.ck[data-refeicao="${refeicao}"]`);// Seleciona o checkbox correspondente à refeição na linha atual
                if (checkbox) checkbox.checked = true;// Se o checkbox existir, marca-o como selecionado

                }
            });
            }
        } catch (error) {
            console.error('Error parsing JSON data:', error);
        }
        });
        
        document.getElementById('diaselecionado').innerHTML = 'Data: ' + formatter.format(date);
    }
    function saveTable(){
        var table = document.getElementById('tabela');
        var rows = table.rows;
        var data = [];
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
