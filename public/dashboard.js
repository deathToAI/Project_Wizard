document.addEventListener('DOMContentLoaded', function() {

    function updateSelections() {
        const idUsuario = document.getElementById('idusuario').dataset.id; //Pega o id do usuário do elemento com id "idusuario"
        const tabela = document.getElementById('tabela'); // Define a tabela onde as refeições estão listadas
        
        let listaDias = []; //Inicializa um array para armazenar as datas
        tabela.querySelectorAll('.dia').forEach(function(dia){
            listaDias.push(dia.getAttribute('value')); //Pega o valor do dia e adiciona à array data
        })
        fetch(`user_retrieve.php?dias=${encodeURIComponent(JSON.stringify(listaDias))}&id=${encodeURIComponent(idUsuario)}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(function(item) {
                const dia = item.data_refeicao; //Pega a data do item
                const refeicoes = item.refeicao.split(',');
                if (refeicoes.includes('cafe')) {
                    linha.querySelector('input[name="cafe"]').checked = true;
                }
                if (refeicoes.includes('almoco')) {
                    linha.querySelector('input[name="almoco"]').checked = true;
                }
                if (refeicoes.includes('janta')) {
                    linha.querySelector('input[name="janta"]').checked = true;
                }
            });
        })
        .catch(error => {
            console.error('Erro ao buscar informações do usuário:', error);
            alert(`Erro ao buscar informações do usuário: ${error}`);
        });
    

        //Define linhas
        for (const i = 0 ; i<tabela.rows.length; i++){
        const linha = tabela.rows[i];
        
            //Define células
            for (const j= 0 ; j<linha.cells.length; j++){
            const cell = linha.cells[j]; //
            const checkbox= cell.querySelector('input[type=checkbox]') ; //Define a checkbox de cada celula
            const dia = checkbox.getAttribute('value'); //Pega o valor do dia da célula
                switch (refeicao) {
                    case 'cafe':
                    if (checkbox.name == "cafe" && checkbox.checked) { // Se a checkbox for de café da manhã e estiver marcada
                        selecionados[dia] = { "cafe": true}; // Adiciona ao objeto JSON
                    }
                    break;
                    case 'almoco':
                    if (checkbox.name == "almoco" && checkbox.checked) { // Se a checkbox for de almoco da tarde e estiver marcada
                        selecionados[dia] = {  "almoco": true }; // Adiciona ao objeto JSON
                    }
                    break;
                    case 'janta':
                    if (checkbox.name == "janta" && checkbox.checked) { // Se a checkbox for de jantar da noite e estiver marcada
                        selecionados[dia] = {"janta": true }; // Adiciona ao objeto JSON
                    }
                    break;
                } // fim do switch
            }//fim do for j
        } // fim do for i
    } // fim da função updateSelections

    updateSelections(); // Chama a função para atualizar as seleções ao carregar a página
});