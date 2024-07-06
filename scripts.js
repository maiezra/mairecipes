document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('signUpForm');
    form.addEventListener('submit', function(event) {
        var password = document.getElementById('password').value;
        var confirm_password = document.getElementById('confirm_password').value;

        if (password !== confirm_password) {
            event.preventDefault();
            alert('Passwords do not match. Please try again.');
        }
    });
});document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var recipeId = this.getAttribute('data-recipe-id');
            var isApi = this.getAttribute('data-is-api');

            fetch('like_recipe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'recipe_id=' + recipeId + '&is_api=' + isApi
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.liked) {
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-primary');
                        this.innerText = 'Liked';
                    } else {
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-outline-primary');
                        this.innerText = 'Like';
                    }
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
