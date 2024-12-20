<?php
include 'koneksi.php'; // Make sure koneksi.php is in the same directory

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['fetch_data'])) {
    // Initialize response array
    $response = [
        "customers_per_year" => []
    ];

    // Query to get total customers per year
    $query_customers = "
        SELECT YEAR(OrderDate) AS year, COUNT(DISTINCT CustomerID) AS total_customers
        FROM sales_salesorderheader
        GROUP BY YEAR(OrderDate)
        ORDER BY YEAR(OrderDate);
    ";

    $result_customers = mysqli_query($conn, $query_customers);

    if ($result_customers) {
        while ($row = mysqli_fetch_assoc($result_customers)) {
            $response["customers_per_year"][] = $row;
        }
    } else {
        die(json_encode(["error" => "Error in customers query: " . mysqli_error($conn)]));
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
    <title>Total Pelanggan Tiap Tahun</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
            display: flex;
            justify-content: left;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        .chart-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
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
            <h2>Total Pelanggan Tiap Tahun</h2>
            <canvas id="customersChart"></canvas>
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

                // Data for the customer chart per year
                const customersPerYear = data.customers_per_year;
                const customerLabels = customersPerYear.map(item => item.year);
                const customerValues = customersPerYear.map(item => item.total_customers);

                // Create the customer chart
                new Chart(document.getElementById('customersChart'), {
                    type: 'bar',
                    data: {
                        labels: customerLabels,
                        datasets: [{
                            label: '',
                            data: customerValues,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
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
