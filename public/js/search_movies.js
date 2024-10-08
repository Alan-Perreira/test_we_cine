    
    const searchInput = document.getElementById('search');
    const resultsDiv = document.getElementById('search-results');
    const moviesContainer = document.getElementById('movies-container');
    
    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        console.log(query);
        
        console.log("hhdhhdhd");
        
        if (query.length > 2) {
            fetch(`{{ path('search_movie') }}?query=` + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = ''; // Efface les résultats précédents
                    moviesContainer.innerHTML = ''; // Efface les films précédents

                    if (data.results && data.results.length > 0) {
                        // Limiter à 5 films
                        const moviesToShow = data.results.slice(0, 5);

                        // Créer un tableau de promesses pour récupérer les vidéos de chaque film
                        const videoPromises = moviesToShow.map(movie => {
                            return fetch(`https://api.themoviedb.org/3/movie/${movie.id}/videos?api_key=d4ed2bbb0af16f68385f3faae738a21f`)
                                .then(response => response.json())
                                .then(videoData => {
                                    const trailerUrl = videoData.results.find(video => video.type === 'Trailer' && video.site === 'YouTube');
                                    return { ...movie, trailerUrl: trailerUrl ? `https://www.youtube.com/embed/${trailerUrl.key}` : null };
                                });
                        });

                        // Attendre que toutes les promesses soient résolues
                        Promise.all(videoPromises).then(moviesWithTrailers => {
                            moviesWithTrailers.forEach(movie => {
                                const trailerUrl = movie.trailerUrl;

                                // Créer une carte de film pour chaque film
                                const movieCard = `
                                    <div class="movie-card">
                                        <h3>${movie.title}</h3>
                                        <p>${movie.overview}</p>
                                        <h3>Bande annonce</h3>
                                        ${trailerUrl ? `<iframe width="50%" height="315" src="${trailerUrl}" frameborder="0" allowfullscreen></iframe>` : '<p>Aucune bande-annonce disponible.</p>'}
                                    </div>
                                `;
                                moviesContainer.innerHTML += movieCard;
                            });
                        });
                    } else {
                        resultsDiv.innerHTML = '<div>Aucun résultat trouvé</div>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des films:', error);
                    resultsDiv.innerHTML = '<div>Erreur lors de la recherche</div>';
                });
        }
    });

