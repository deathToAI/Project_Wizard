// admin.js
document.addEventListener('DOMContentLoaded', function() {
    // Manipulador para o modal de edição
    document.querySelectorAll('[data-action="edit"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const userData = JSON.parse(this.dataset.user);
            openEditModal(userData);
        });
    });
    
    // Manipulador para o botão de fechar
    document.querySelector('.close-btn').addEventListener('click', closeModal);
    
    // Fechar modal ao clicar fora
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
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
    
    // Debug: verifique no console os valores sendo enviados
    // console.log('Dados do usuário:', {
    //     id: user.id,
    //     username: user.username,
    //     nome_pg: user.nome_pg,
    //     role: user.role,
    //     grupo: user.grupo,
    //     token: user.token
    // });
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