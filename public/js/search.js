document.getElementById('search').addEventListener('input', function() {
    let query = this.value;

    if (query.length > 2) {
        fetch(`/search?query=` + encodeURIComponent(query))
        .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur de réseau : ${response.status} - ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                let resultsDiv = document.getElementById('search-results');
                resultsDiv.innerHTML = ''; // Efface les résultats précédents
                let moviesContainer = document.getElementById('movies-container');
                moviesContainer.innerHTML = ''; // Efface les films précédents pour afficher les nouveaux résultats

                if (data.results && data.results.length > 0) {
                    let moviesToShow = data.results.slice(0, 5);

                    let videoPromises = moviesToShow.map(movie => {
                        return fetch(`https://api.themoviedb.org/3/movie/${movie.id}/videos?api_key=d4ed2bbb0af16f68385f3faae738a21f`)
                            .then(response => response.json())
                            .then(videoData => {
                                let trailerUrl = videoData.results.find(video => video.type === 'Trailer' && video.site === 'YouTube');
                                return { ...movie, trailerUrl: trailerUrl ? `https://www.youtube.com/embed/${trailerUrl.key}` : null };
                            });
                    });

                    Promise.all(videoPromises).then(moviesWithTrailers => {
                        moviesWithTrailers.forEach(movie => {
                            const trailerUrl = movie.trailerUrl;
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
                console.error('Erreur lors de la récupération des données :', error);
                document.getElementById('search-results').innerHTML = '<div>Une erreur est survenue lors de la recherche.</div>';
            });
    }
});
