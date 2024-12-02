document.addEventListener('DOMContentLoaded', function () {
    const resultContainer = document.getElementById('result');
    const countryInput = document.getElementById('country');

    
    function fetchData(endpoint) {
        fetch(endpoint)
            .then(response => response.text())
            .then(data => {
                resultContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                resultContainer.innerHTML = 'Error fetching data';
            });
    }

    
    document.getElementById('lookup').addEventListener('click', function () {
        const country = countryInput.value.trim(); 
        fetchData(`world.php?country=${encodeURIComponent(country)}`);
    });

    
    document.getElementById('citylookup').addEventListener('click', function () {
        const country = countryInput.value.trim();
        fetchData(`world.php?country=${encodeURIComponent(country)}&lookup=cities`);
    });
});
