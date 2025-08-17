document.addEventListener('DOMContentLoaded', function() {

    function updateSelections() {
        //Verifica o banco de dados para ver quais dias estão marcados
        // Enviar id, datas 
        //{"id" : "x" , "data" :"2025-08-20"}
        //{"id" : "x" , "data" :"2025-08-21"}
        //Receber datas e refeições 
        //{"data": "2025-08-20", "cafe" : true , "almoco" : false , "jantar": false }
        //{"data": "2025-08-21", "cafe" : true , "almoco" : true , "jantar": false }

        //Manipula tabela
        const tabela = document.getElementById('tabela');
        //Define linhas
        for (const i = 0 ; i<tabela.rows; i++){
        const linha = tabela.rows[i];
        const dia = linha.querySelector('#dia') ;
            //Deinfe células
            for (const j= 0 ; j<linha.cells; j++){
            const cell = linha.cells[j]; //
            const checkbox= cell.querySelectorAll('input[type=checkbox]') ; //Deinfe a checkbox de cada celula
            if (checkbox){ // Se a checkbox existe e o banco de dados informou que o usuário está arranchado
                checkbox.checked  // Marca a checkbox para aquela refeição naquele dia
            }
            
            }
        }
    }
});