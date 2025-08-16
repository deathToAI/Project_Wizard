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
        // console.log(`Fetching ${dateValue}`); // Depuração para ver se a data correta está sendo devolvida por retrieve.php
        fetch(`retrieve.php?date=${dateValue}`)
        .then(response => response.text())
        // 
        .then(data => {
        try {
            const jsonData = JSON.parse(data); // Le a resposta em JSON
            // console.log(`Data JSON de UpdateDateHeader:${data}`); //Verificar se o JSON foi lido

            const tabela = document.getElementById('tabela'); // Obtém a tabela com o ID "tabela" do documento HTML
            const tam_tabela = tabela.rows.length; // Obtém o número de linhas da tabela


            for (let i = 2; i < tam_tabela; i++) { // Itera pelas linhas da tabela, começando da terceira linha (índice 2)
                const linha = tabela.rows.item(i);// Obtém as células da linha atual
                const id = linha.getAttribute('data-id');// Obtém o ID da linha atual
                // console.log(`ID da linha: ${id}`);// Mostra o ID da linha no console(depuração)
                const checkboxes = linha.querySelectorAll('.ck');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });// Desmarca todos os checkboxes na linha atual

                jsonData.forEach(element => { // Itera pelos elementos do array "jsonData"
                    // console.log(element.user_id, element.refeicao); // Mostra o ID do usuário e o tipo de refeição no console
                    if (String(element.user_id) === String(id)) {    // Verifica se o ID do usuário corresponde ao ID da linha atual
                        const refeicao = element.refeicao;  // Obtém o tipo de refeição do usuário
                        const checkbox = linha.querySelector(`.ck[data-refeicao="${refeicao}"]`);// Seleciona o checkbox correspondente à refeição na linha atual
                        if (checkbox) checkbox.checked = true ;// Se o checkbox existir, marca-o como selecionado

                    }
                });         
            }
        } catch (error) {
            console.error('Error parsing JSON data:', error);
        }
        });
        
        document.getElementById('diaselecionado').innerHTML = 'Data: ' + formatter.format(date);
    }
    //Função para salvar a tabela
    function saveTable(){
        const tabela = document.getElementById('tabela');
        const tam_tabela = tabela.rows.length;
        const dia = document.getElementById('dia').value;
        const refeicoes= [];
        try{
            for (let  i = 2; i < tam_tabela; i++) { 
                const linha = tabela.rows.item(i); //define a linha
                const id = linha.getAttribute('data-id'); // define o id 
                //definindo quem são as refeições daquele id
                for (let j = 0; j < linha.cells.length; j++) { // Itera pelas células da linha 
                    const ck = linha.cells[j].querySelector('.ck');
                    if (ck && ck.checked) { // Verifica se o checkbox está marcado
                        const refeicao = ck.getAttribute('data-refeicao'); // Obtém o tipo de refeição
                        refeicoes.push({user_id: id, data_refeicao: dia, refeicao: refeicao}); // Adiciona ao array de refeições
                        // console.log(`Refeição selecionada: ${id}, ${dia}, ${refeicao}`); // Depuração: mostra a refeição selecionada no console
                    }
                
                } // Fim do loop pelas células
            } // Fim do loop pelas linhas

            //Envio de dados para o servidor
            console.log(`Refeições a serem salvas: ${JSON.stringify(refeicoes)}`); // Depuração: mostra o array de refeições no console
            
          //  Envia os dados para o servidor
            const payload = JSON.stringify(refeicoes);      // pode ser "[]"
            const corpo = `dia=${encodeURIComponent(dia)}&payload=${encodeURIComponent(payload)}`;

            fetch('http://localhost:3333/config/furriel/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: corpo
            })
            .then(r => r.json())
            .then(({ status, message }) => {
            alert(status === 'success' ? message : `Erro: ${message}`);
            })
            .catch(err => {
            console.error('Erro ao enviar dados:', err);
            alert(`Erro ao enviar dados: ${err}`);
            });
                    
        }//fim do try
       
        catch(e){
            alert(`Erro ao salvar tabela: ${e}`);
            console.error(`Erro ao salvar tabela: ${e}`);
        }
    }

    const tudoCafe = document.getElementById('tudoCafe');
    const tudoAlmoco = document.getElementById('tudoAlmoco');
    const tudoJanta = document.getElementById('tudoJanta');
    
    tudoCafe.addEventListener('change',   e => setAll('cafe',   e.target.checked));
    tudoAlmoco.addEventListener('change', e => setAll('almoco', e.target.checked));
    tudoJanta.addEventListener('change',  e => setAll('janta',  e.target.checked));
    

    function setAll(refeicao, checked) {
    const tabela = document.getElementById('tabela');
    if (!tabela) return;

    const checkboxes = tabela.querySelectorAll(`.ck[data-refeicao="${refeicao}"]`);
    checkboxes.forEach(cb => { cb.checked = checked; });
    }
        
    
    // Inicialização
    var selectElement = document.getElementById('dia');
    if(selectElement) {
        updateDateHeader(selectElement.value);
        
        // Adiciona o event listener para mudanças
        selectElement.addEventListener('change', function() {
            updateDateHeader(this.value);
        });
    var enviar = document.getElementById('btn_enviar');
    enviar.addEventListener('click', saveTable);

}
});
