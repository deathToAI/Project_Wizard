document.addEventListener('DOMContentLoaded', function() {

    function updateSelections() {
        const idUsuario = document.getElementById('idusuario').dataset.id; //Pega o id do usuário do elemento com id "idusuario"
        const tabela = document.getElementById('tabela'); // Define a tabela onde as refeições estão listadas
        //console.log('ID do usuário:', idUsuario); // Log do ID do usuário para depuração
        
        let listaDias = []; //Inicializa um array para armazenar as datas
        tabela.querySelectorAll('.dia').forEach(function(dia){
            listaDias.push(dia.getAttribute('value')); //Pega o valor do dia e adiciona à array data
        })

        //console.log('Lista de dias:', listaDias); // Log da lista de dias para depuração
        const url = 'user_retrieve.php'
        + '?dias=' + encodeURIComponent(JSON.stringify(listaDias))
        + '&id=' + encodeURIComponent(idUsuario);
        console.log('URL da requisição:', url); // Log da URL da requisição para depuração
        //Pega as refeições do usuário
        fetch(`user_retrieve.php?dias=${encodeURIComponent(JSON.stringify(listaDias))}&id=${encodeURIComponent(idUsuario)}`)
        .then(response => response.json())
        .then(data => {
            console.log('Dados recebidos:', data); // Log dos dados recebidos para depuração
            data.forEach(function(item) {
                const dia = item.data_refeicao; //Pega a data do item
                console.log('Processando data:', dia); // Log da data sendo processada para depuração
                const cellDia = tabela.querySelector('td.dia[value="' + String(dia).trim() +  '"]'); //Encontra a célula correspondente à data
                if (!cellDia) return; //Se não encontrar a célula, retorna
                const linha = cellDia.parentElement; //Pega a linha da célula
                console.log('Linha encontrada para a data', dia, ':', linha); // Log da linha encontrada para depuração

                const refeicoes = (item.refeicao ?? item.refeicoes ?? '')
                .split(',')
                .map(r => r.trim())
                .filter(Boolean);
                console.log('Refeições para a data', dia, ':', refeicoes); // Log das refeições para depuração

                if (refeicoes.includes('cafe'))   { linha.querySelector('input[name="cafe"]').checked   = true; }
                if (refeicoes.includes('almoco')) { linha.querySelector('input[name="almoco"]').checked = true; }
                if (refeicoes.includes('janta'))  { linha.querySelector('input[name="janta"]').checked  = true; }

            });
        })
        .catch(error => {
            console.error('Erro ao buscar informações do usuário:', error);
            alert(`Erro ao buscar informações do usuário: ${error}`);
        });
    

        //Define linhas
        for (let i = 1 ; i<tabela.rows.length; i++){
        const linha = tabela.rows[i];
            //Define células
            for (let j=1 ; j<linha.cells.length; j++){
            const cell = linha.cells[j]; // itera pelas células da linha
            const checkbox= cell.querySelector('input[type=checkbox]') ; //Define a checkbox de cada celula
            if (!checkbox) continue;
            const dia = checkbox.getAttribute('value'); //Pega o valor do dia da célula
                if (checkbox.name === "cafe" && checkbox.checked) {
                    selecionados[dia] = { "cafe": true };
                }
                if (checkbox.name === "almoco" && checkbox.checked) {
                    selecionados[dia] = { "almoco": true };
                }
                if (checkbox.name === "janta" && checkbox.checked) {
                    selecionados[dia] = { "janta": true };
                } 
            }//fim do for j
        } // fim do for i
    } // fim da função updateSelections

    updateSelections(); // Chama a função para atualizar as seleções ao carregar a página
});