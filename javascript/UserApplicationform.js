document.getElementById('addItem').addEventListener('click', function () {
    var container = document.getElementById('itemContainer');
    var newRow = document.createElement('div');
    newRow.classList.add('item-row');

    newRow.innerHTML = `
        <label for="itemType[]">Item Type:</label>
        <input type="text" name="itemType[]" required>

        <label for="quantity[]">Quantity:</label>
        <input type="number" name="quantity[]" required>
    `;
    container.appendChild(newRow);
});
