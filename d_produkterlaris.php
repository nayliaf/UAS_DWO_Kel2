<?php
include 'koneksi.php'; // Make sure koneksi.php is in the same directory

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['fetch_data'])) {
    // Initialize response array
    $response = [
        "top_products" => []
    ];

    // Query to get top-selling products
    $query_products = "
        SELECT p.Name AS product_name, SUM(sod.OrderQty) AS total_quantity
        FROM sales_salesorderdetail sod
        JOIN production_product p ON sod.ProductID = p.ProductID
        GROUP BY p.Name
        ORDER BY total_quantity DESC
        LIMIT 10;
    ";

    $result_products = mysqli_query($conn, $query_products);

    if ($result_products) {
        while ($row = mysqli_fetch_assoc($result_products)) {
            $response["top_products"][] = $row;
        }
    } else {
        die(json_encode(["error" => "Error in products query: " . mysqli_error($conn)]));
    }

    // Return results in JSON format
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Terlaris di Adventure Works</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        .chart-container {
            max-width: 800px;
            margin: 0px auto;
            padding: 50px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        canvas {
            width: 100% !important;
            height: 500px !important;
        }

        .content {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .chart-container h2 {
            text-align: center;
            color: #333;
            font-size: 1.2em;
        }

        .error-message {
            color: red;
            text-align: center;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="chart-container">
            <h2>Produk Terlaris di Adventure Works</h2>
            <canvas id="productsChart"></canvas>
        </div>
    </div>

    <script>
        fetch('?fetch_data=true')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data fetched:', data);

                // Data for the top-selling products
                const topProducts = data.top_products;
                const productLabels = topProducts.map(item => item.product_name);
                const productValues = topProducts.map(item => item.total_quantity);

                // Create the top-selling products chart
                new Chart(document.getElementById('productsChart'), {
                    type: 'pie',
                    data: {
                        labels: productLabels,
                        datasets: [{
                            label: 'Top Products',
                            data: productValues,
                            backgroundColor: [
                                'rgb(218, 73, 92)',
                                'rgb(253, 129, 104)',
                                'rgb(252, 210, 103)',
                                'rgb(171, 213, 126)',
                                'rgb(99, 209, 181)',
                                'rgb(144, 214, 209)',
                                'rgb(109, 202, 235)',
                                'rgb(114, 168, 239)',
                                'rgb(147, 125, 194)',
                                'rgb(238, 163, 206)'
                            ],
                            borderColor: [
                                'rgb(168, 62, 85)',
                                'rgb(195, 116, 88)',
                                'rgb(218, 185, 101)',
                                'rgb(128, 164, 89)',
                                'rgb(74, 164, 142)',
                                'rgb(107, 172, 168)',
                                'rgb(75, 154, 183)',
                                'rgb(87, 133, 192)',
                                'rgb(116, 94, 164)',
                                'rgb(179, 107, 148)',
                                'rgba(216, 51, 74, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.body.innerHTML = '<div class="error-message">Error fetching data. Please try again later.</div>';
            });
    </script>
</body>
</html>
