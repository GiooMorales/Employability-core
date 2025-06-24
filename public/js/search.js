document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchResults = document.createElement('div');
    searchResults.className = 'search-results';
    searchInput.parentNode.appendChild(searchResults);

    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    
                    if (data.users.length === 0) {
                        searchResults.innerHTML = '<div class="no-results">Nenhum usuário encontrado</div>';
                    } else {
                        data.users.forEach(user => {
                            const userElement = document.createElement('div');
                            userElement.className = 'search-result-item';
                            userElement.innerHTML = `
                                <a href="/perfil/${user.id_usuarios}" class="search-result-link">
                                    <img src="${user.url_foto ? '/storage/' + user.url_foto : '/images/default-avatar.png'}" 
                                         alt="${user.nome}" 
                                         class="search-result-avatar">
                                    <div class="search-result-info">
                                        <div class="search-result-name">${user.nome}</div>
                                        <div class="search-result-details">
                                            ${user.titulo ? user.titulo + ' • ' : ''}
                                            ${user.cidade ? user.cidade + ', ' : ''}
                                            ${user.estado || ''}
                                        </div>
                                    </div>
                                </a>
                            `;
                            searchResults.appendChild(userElement);
                        });
                    }
                    
                    searchResults.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erro na pesquisa:', error);
                });
        }, 300);
    });

    // Fechar resultados ao clicar fora
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
}); 