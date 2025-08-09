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