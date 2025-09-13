
        // Initialisation de DataTable
        $(document).ready(function() {
            $('#productsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json',
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    info: 'Affichage de _START_ à _END_ sur _TOTAL_ entrées',
                    lengthMenu: 'Afficher _MENU_ entrées par page',
                    search: 'Rechercher:',
                    searchPlaceholder: 'Rechercher...',
                    infoEmpty: 'Aucune entrée à afficher',
                    infoFiltered: '(filtré de _MAX_ entrées au total)'
                },
                order: [[1, 'asc']], // Tri par nom par défaut
                pageLength: 15,
                dom: "<'row mb-4'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-4'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                     .replace(/l>/g, 'l class="mb-3">')
                     .replace(/f>/g, 'f class="mb-3">'),
                columnDefs: [
                    { orderable: false, targets: [0, 7] }, // Désactive le tri sur les colonnes d'image et d'actions
                    { searchable: false, targets: [0, 5, 6, 7] } // Désable la recherche sur ces colonnes
                ],
                drawCallback: function() {
                    $('.paginate_button').addClass('btn btn-sm btn-outline-secondary mx-1');
                    $('.paginate_button.current').removeClass('btn-outline-secondary').addClass('btn-primary');
                }
            });

            // Disparition automatique du message flash après 5 secondes
            var flash = document.getElementById('flash-success');
            if (flash) {
                setTimeout(function() {
                    $(flash).fadeOut('slow');
                }, 5000);
            }

            // Confirmation avant suppression
            $('.delete').on('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                    e.preventDefault();
                }
            });
        });
