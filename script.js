jQuery(document).ready(function($) {
    $('#rick-and-morty-form').on('submit', function(event) {
        event.preventDefault(); 
        console.log('Form submitted'); 

        var query = $('#rick-and-morty-query').val();
        console.log('Query:', query); 

        $.ajax({
            url: queryParams.ajax_url,
            type: 'GET',
            data: {
                action: 'script',
                query: query,
                nonce: queryParams.nonce
            },
            success: function(response) {
                console.log('AJAX success:', response);
                if (response.success) {
                    var results = response.data.results;
                    var output = '';

                    if (results) { 
                        output = '<ul>'; 
                        
                        $.each(results, function(index, result) { 
                            output += '<li>'; 
                            output += '<h2>' + result.name + '</h2>'; 
                            output += '<p>Status: ' + result.status + '</p>'; 
                            output += '<p>Species: ' + result.species + '</p>'; 
                            output += '<p>Type: ' + result.type + '</p>'; 
                            output += '<p>Gender: ' + result.gender + '</p>'; 
                            output += '<p>Origin: ' + result.origin.name + '</p>'; 
                            output += '<p>Location: ' + result.location.name + '</p>'; 
                            output += '<img src="' + result.image + '" alt="' + result.name + '">'; 
                            output += '</li>'; 
                        }); 
                        
                        output += '</ul>'; 
                    } else { 
                        output = '<h2>No characters match your search. Try again!</h2>'; 
                    }

                    $('#rick-and-morty-query-results').html(output);
                } else {
                    $('#rick-and-morty-query-results').html('<p>' + response.data + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error); 
                $('#rick-and-morty-query-results').html('<p>Error: ' + error + '</p>');
            }
        });
    });
});
