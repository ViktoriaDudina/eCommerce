// PLUS AND MINUS ITEM

$(document).ready(function () {
    $('#minusButton').click(function (e) {
        e.preventDefault();
        var inputField = $(this).next('input');
        var currentValue = parseInt(inputField.val());

        if (!isNaN(currentValue) && currentValue > 1) {
            inputField.val(currentValue - 1);
        }
    });

    $('#plusButton').click(function (e) {
        e.preventDefault();
        var inputField = $(this).prev('input');
        var currentValue = parseInt(inputField.val());

        if (!isNaN(currentValue)) {
            inputField.val(currentValue + 1);
        }
    });
});

// Customer Porducts Review

document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.getElementById('commentForm');

    commentForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent form submission to avoid page reload

        // Get the comment text from the textarea
        const commentText = document.getElementById('textAreaExample').value;

        if (commentText.trim() !== '') { // Check if the comment is not empty
            // Create a new element for the comment
            const newComment = document.createElement('div');
            newComment.classList.add('card', 'mb-4');
            newComment.innerHTML = `
                <div class="card-body">
                    <p>${commentText}</p>
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-row align-items-center">
                            <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/img%20(32).webp" alt="avatar" width="25" height="25"/>
                            <p class="small mb-0 ms-2">New User</p>
                        </div>
                    </div>
                </div>
            `;

            // Add the new comment to the beginning of the comments section
            const commentsSection = document.querySelector('.card-body.p-4'); // Or specify another selector
            commentsSection.prepend(newComment);

            // Reset the textarea value after adding the comment
            document.getElementById('textAreaExample').value = '';
        } else {
            // Display an error message if the comment is empty
            alert('Please enter a comment before submitting.');
        }
    });
});
