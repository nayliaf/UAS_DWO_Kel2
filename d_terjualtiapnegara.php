<?php
// Handle back-end logic when the request is made to fetch data
if (isset($_GET['fetch_data'])) {
    header('Content-Type: application/json');

    include 'koneksi.php'; // Make sure koneksi.php is in the same directory

    // Query to calculate total sales by territory (country) and year, limited to 2011-2014
    $query = "
        SELECT st.Name AS country, YEAR(soh.OrderDate) AS year, SUM(soh.TotalDue) AS total_sales
        FROM sales_salesorderheader soh
        JOIN sales_salesperson sp ON soh.SalesPersonID = sp.BusinessEntityID
        JOIN sales_salesterritory st ON sp.TerritoryID = st.TerritoryID
        WHERE YEAR(soh.OrderDate) BETWEEN 2011 AND 2014
        GROUP BY st.Name, YEAR(soh.OrderDate)
        ORDER BY st.Name, YEAR(soh.OrderDate);
    ";

    $result = mysqli_query($conn, $query);

    // Check query execution
    if (!$result) {
        die(json_encode(["error" => "Query execution failed: " . mysqli_error($conn)]));
    }

    // Fetch data into an array
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['country']][] = [
            "year" => intval($row["year"]),
            "total_sales" => floatval($row["total_sales"])
        ];
    }

    // Output the data in JSON format
    echo json_encode($data);

    // Close the connection
    mysqli_close($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan Tiap Negara (2011-2014)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin-bottom: 30px;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: left;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 20px;
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .chart-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        canvas {
            width: 100% !important;
            max-width: 1000px;
            height: 600px !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Penjualan Tiap Negara (2011-2014)</h1>
        <div class="chart-wrapper">
            <canvas id="salesByCountryYearChart"></canvas>
        </div>
    </div>
    <script>
        // Fetch data from the server and render the chart
        fetch('?fetch_data=true')
            .then(response => response.json())
            .then(data => {
                const allYears = [2011, 2012, 2013, 2014]; // Explicitly define years
                const labels = allYears; // X-axis labels
                const datasets = []; // To store the datasets
                
                let colorIndex = 0;
                const colors = [
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
                ];

                Object.keys(data).forEach(country => {
                    let salesData = allYears.map(year => {
                        const yearData = data[country].find(item => item.year === year);
                        return yearData ? yearData.total_sales : 0;
                    });

                    datasets.push({
                        label: country,
                        data: salesData,
                        backgroundColor: colors[colorIndex % colors.length],
                        borderColor: colors[colorIndex % colors.length].replace('0.2', '1'),
                        borderWidth: 1
                    });

                    colorIndex++;
                });

                // Configure the bar chart
                const ctx = document.getElementById('salesByCountryYearChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: allYears,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: (context) => `IDR ${context.raw.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Year'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Total Sales (USD)'
                                },
                                ticks: {
                                    callback: value => `$ ${value.toLocaleString()}`
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Failed to fetch sales data.');
            });
    </script>
</body>
</html>