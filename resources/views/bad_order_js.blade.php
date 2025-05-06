<script>
        function confirmSetInactive() {
            return confirm("Are you sure you want to update the product status?")
        }

        function deleteBO(obId) {
            var boId = obId;
            $('#modal-delete').modal('show');
            $('#modal-delete').find('input[name="bo_id"]').val(boId);

        }
    </script>
    <script>
        function printPage(button) {
            var boId = button.getAttribute('data-bo-id');

            // AJAX request to fetch BO details
            fetch(`/getBoDetails/${boId}`)
                .then(response => response.json())
                .then(data => {

                    badOrder = data["badOrder"];
                    dbProducts = data["products"];

                    // Assuming data is an array of items
                    var products = dbProducts.map(item => `
                            <tr>
                                <td>${item.ptype_code}</td>
                                <td>${item.quantity}</td>
                                <td align="right">${formatNumber(item.quantity * item.price)}</td>
                            </tr>
                        `).join('');

                    var totalAmount = data["amount"];
                    var created = badOrder["created_at"];
                    // Create a Date object from the string
                    var dateObj = new Date(created);

                    // Extract the date components
                    var month = dateObj.getMonth() + 1; // Month is zero-based, so we add 1
                    var day = dateObj.getDate();
                    var year = dateObj.getFullYear();

                    // Format the date as MM-DD-YYYY
                    var formattedDate =
                        `${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}-${year}`;
                    var boperc = badOrder["bo_percentage"];
                    var lessamt = totalAmount * (boperc / 100);
                    var netamt = totalAmount - lessamt;

                    var fname = badOrder.customer.firstname;
                    var lname = badOrder.customer.lastname;
                    // Create a new window for printing
                    var mywindow = window.open('', 'PRINT', 'height=900,width=1000');
                    mywindow.document.write('<html><head><title>BAD ORDER SLIP</title>');
                    mywindow.document.write('<style>');
                    mywindow.document.write(
                        'body{ font-family:"Arial",Helvetica,sans-serif;font-size: 9pt;word-wrap: break-word; }');
                    mywindow.document.write('hr { border: 0; border-top: 1px solid #000; margin: 10px 0; }');
                    mywindow.document.write(
                        'td { font-family:"Arial",Helvetica,sans-serif;font-size: 9pt;word-wrap: break-word; }');
                    mywindow.document.write('</style>');
                    mywindow.document.write('</head><body>');
                    mywindow.document.write('<center>EOLF FOOD TRADING OPC</center><br>');
                    mywindow.document.write('<center>BAD ORDER SLIP</center><br>');
                    mywindow.document.write('<br><br>');
                    mywindow.document.write('BO no.:  ' + boId + '<br>');
                    mywindow.document.write('Date:  ' + formattedDate + '<br>');
                    mywindow.document.write('Customer: ' + lname + ', ' + fname + '<br>');
                    mywindow.document.write('<table width="100%">');
                    mywindow.document.write('<tr><td>Items</td><td>Pcs</td><td align="right">Amount</td></tr>');
                    mywindow.document.write(products);
                    mywindow.document.write('</table>');
                    mywindow.document.write('<hr>');
                    mywindow.document.write('<table width="100%">');
                    mywindow.document.write('<tr><td align="left">Sub-total:</td><td align="right">' + formatNumber(
                        totalAmount) + '</td></tr>');
                    mywindow.document.write('<tr><td align="left">BO Amount Due:</td><td align="right">' + formatNumber(
                        lessamt) + '</td></tr>');
                    mywindow.document.write('<tr><td align="left">BO (%):</td><td align="right">' + boperc +
                        '%</td></tr>');
                    mywindow.document.write('<tr><td align="left">Total Amount:</td><td align="right">' + formatNumber(
                        netamt) + '</td></tr>');
                    mywindow.document.write('</table>');
                    mywindow.document.write('Received By:<br>');
                    mywindow.document.write('<br><br><br>');
                    mywindow.document.write('---------------------------------<br>');
                    mywindow.document.write('Signature over printed name<br>');
                    mywindow.document.write('</body></html>');

                    mywindow.document.close(); // Necessary for IE >= 10
                    mywindow.focus(); // Necessary for IE >= 10

                    mywindow.print();
                    mywindow.close();
                })
                .catch(error => console.error('Error:', error));


        }

        function formatNumber(amount) {
            // Ensure amount is a number and convert it to a string with fixed two decimal places
            var formattedAmount = parseFloat(amount).toFixed(2);

            // Split the number into integer and decimal parts
            var parts = formattedAmount.split('.');
            var integerPart = parts[0];
            var decimalPart = parts.length > 1 ? '.' + parts[1] : '';

            // Add commas for thousands in the integer part
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            // Combine integer part and decimal part
            return integerPart + decimalPart;
        }
    </script>