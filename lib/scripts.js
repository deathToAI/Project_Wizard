document.addEventListener('DOMContentLoaded', function() {

    //==============================================//
    //==         CÓDIGO GLOBAL (Relógio)          ==//
    //==============================================//
    const relogioEl = document.getElementById('relogio');
    if (relogioEl) {
        function atualizarRelogio() {
            const agora = new Date();
            // Usar toLocaleTimeString é mais simples e respeita a localidade do usuário
            relogioEl.textContent = agora.toLocaleTimeString('pt-BR');
        }
        // Inicia o relógio imediatamente e depois atualiza a cada segundo
        atualizarRelogio();
        setInterval(atualizarRelogio, 1000);
    }


    //==============================================//
    //== CÓDIGO PARA A PÁGINA DE ADMIN (admin.php) ==//
    //==============================================//
    if (document.getElementById('editModal')) {
        // Manipulador para o modal de edição
        document.querySelectorAll('[data-action="edit"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const userData = JSON.parse(this.dataset.user);
                openEditModal(userData);
            });
        });

        // Manipulador para o botão de fechar
        document.querySelector('.close-btn').addEventListener('click', closeModal);
        document.querySelector('.btn-secondary').addEventListener('click', closeModal);
        // Fechar modal ao clicar fora
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this.parentNode) {
                closeModal();
            }
        });

        function openEditModal(user) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');

            // Preenche os campos do formulário com os dados do usuário
            form.elements.id.value = user.id;
            form.elements.username.value = user.username;
            form.elements.nome_pg.value = user.nome_pg;
            form.elements.role.value = user.role;
            form.elements.grupo.value = user.grupo;

            // Mantém o token existente no formulário
            user.token = form.elements.token.value;

            // Exibe o modal
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        document.querySelectorAll('.delete-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Tem certeza?')) {
                    e.preventDefault();
                }
            });
        });
    }

    //==================================================//
    //== CÓDIGO PARA A PÁGINA DE FURRIEL (furriel.php) ==//
    //==================================================//
    // Usamos 'seltudo' como guarda, pois é um ID único da página do furriel.
    if (document.getElementById('seltudo')) {
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

            fetch(`retrieve.php?date=${dateValue}`)
                .then(response => response.text())
                .then(data => {
                    try {
                        const jsonData = JSON.parse(data);
                        const tabela = document.getElementById('tabela');
                        const tam_tabela = tabela.rows.length;

                        for (let i = 2; i < tam_tabela; i++) {
                            const linha = tabela.rows.item(i);
                            const id = linha.getAttribute('data-id');
                            const checkboxes = linha.querySelectorAll('.ck');
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = false;
                            });

                            jsonData.forEach(element => {
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
        //Função para salvar a tabela
        function saveTable() {
            const tabela = document.getElementById('tabela');
            const tam_tabela = tabela.rows.length;
            const dia = document.getElementById('dia').value;
            const refeicoes = [];
            try {
                for (let i = 2; i < tam_tabela; i++) {
                    const linha = tabela.rows.item(i);
                    const id = linha.getAttribute('data-id');
                    for (let j = 0; j < linha.cells.length; j++) {
                        const ck = linha.cells[j].querySelector('.ck');
                        if (ck && ck.checked) {
                            const refeicao = ck.getAttribute('data-refeicao');
                            refeicoes.push({ user_id: id, data_refeicao: dia, refeicao: refeicao });
                        }
                    }
                }

                const payload = JSON.stringify(refeicoes);
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

            } catch (e) {
                alert(`Erro ao salvar tabela: ${e}`);
                console.error(`Erro ao salvar tabela: ${e}`);
            }
        }

        const tudoCafe = document.getElementById('tudoCafe');
        const tudoAlmoco = document.getElementById('tudoAlmoco');
        const tudoJanta = document.getElementById('tudoJanta');

        tudoCafe.addEventListener('change', e => setAll('cafe', e.target.checked));
        tudoAlmoco.addEventListener('change', e => setAll('almoco', e.target.checked));
        tudoJanta.addEventListener('change', e => setAll('janta', e.target.checked));

        function setAll(refeicao, checked) {
            const tabela = document.getElementById('tabela');
            if (!tabela) return;
            const checkboxes = tabela.querySelectorAll(`.ck[data-refeicao="${refeicao}"]`);
            checkboxes.forEach(cb => { cb.checked = checked; });
        }

        var selectElement = document.getElementById('dia');
        if (selectElement) {
            updateDateHeader(selectElement.value);
            selectElement.addEventListener('change', function() {
                updateDateHeader(this.value);
            });
            var enviar = document.getElementById('btn_enviar');
            enviar.addEventListener('click', saveTable);
        }
    }

    //====================================================//
    //== CÓDIGO PARA A PÁGINA DE DASHBOARD (dashboard.php) ==//
    //====================================================//
    if (document.getElementById('idusuario')) {
        function updateSelections() {
            const idUsuario = document.getElementById('idusuario').dataset.id;
            const tabela = document.getElementById('tabela');
            let listaDias = [];
            tabela.querySelectorAll('.dia').forEach(function(dia) {
                listaDias.push(dia.dataset.date); // CORREÇÃO: Usar dataset.date
            })

            fetch(`user_retrieve.php?dias=${encodeURIComponent(JSON.stringify(listaDias))}&id=${encodeURIComponent(idUsuario)}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(function(item) {
                        const dia = item.data_refeicao;
                        const cellDia = tabela.querySelector('td.dia[data-date="' + String(dia).trim() + '"]'); // CORREÇÃO: Usar seletor de atributo data-date
                        if (!cellDia) return;
                        const linha = cellDia.parentElement;
                        const refeicoes = (item.refeicao ?? item.refeicoes ?? '').split(',').map(r => r.trim()).filter(Boolean);
                        if (refeicoes.includes('cafe')) { linha.querySelector('input[name="cafe"]').checked = true; }
                        if (refeicoes.includes('almoco')) { linha.querySelector('input[name="almoco"]').checked = true; }
                        if (refeicoes.includes('janta')) { linha.querySelector('input[name="janta"]').checked = true; }
                    });
                })
                .catch(error => {
                    console.error('Erro ao buscar informações do usuário:', error);
                    alert(`Erro ao buscar informações do usuário: ${error}`);
                });
        }

        function saveUserTable() {
            const tabela = document.getElementById('tabela');
            const idUsuario = document.getElementById('idusuario').dataset.id;
            const linhas = tabela.rows;
            const dados = { id: idUsuario, cafe: [], almoco: [], janta: [] };

            for (let i = 1; i < linhas.length; i++) {
                const linha = linhas[i];
                const celulas = linha.cells;
                for (let j = 1; j < celulas.length; j++) {
                    const celula = celulas[j];
                    const checkbox = celula.querySelector('input[type="checkbox"]');
                    if (checkbox && checkbox.checked) {
                        const data = checkbox.value;
                        const refeicao = checkbox.name;
                        if (dados[refeicao]) {
                            dados[refeicao].push(data);
                        }
                    }
                }
            }

            fetch('user_update.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dados)
                })
                .then(response => {
                    if (!response.ok) { throw new Error('A resposta do servidor não foi OK'); }
                    // Se a resposta for um redirecionamento, o navegador seguirá, mas se for JSON, processamos.
                    alert("Formulário enviado com sucesso!");
                    // A página será redirecionada pelo user_update.php, então não precisamos fazer nada aqui.
                })
                .catch((error) => console.error("Erro ao enviar formulário: " + error.message));
        }

        const enviar = document.getElementById('enviar');
        if (enviar) {
            enviar.addEventListener('click', (e) => {
                e.preventDefault();
                saveUserTable();
            });
        }

        updateSelections();
    }
});