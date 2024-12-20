<?php
// Include connection
include 'koneksi.php';

if (isset($_GET['fetch_data'])) {
    header('Content-Type: application/json');

    if (!$conn) {
        echo json_encode(["error" => "Failed to connect to database: " . mysqli_connect_error()]);
        exit();
    }

    // Query to get total sales by category and year
    $query_category_sales = "
        SELECT pc.Name AS category_name, YEAR(soh.OrderDate) AS year, SUM(sod.OrderQty) AS total_sales
        FROM sales_salesorderheader soh
        JOIN sales_salesorderdetail sod ON soh.SalesOrderID = sod.SalesOrderID
        JOIN production_product p ON sod.ProductID = p.ProductID
        JOIN production_productsubcategory psc ON p.ProductSubcategoryID = psc.ProductSubcategoryID
        JOIN production_productcategory pc ON psc.ProductCategoryID = pc.ProductCategoryID
        GROUP BY pc.Name, YEAR(soh.OrderDate)
        ORDER BY pc.Name, YEAR(soh.OrderDate);
    ";

    $result_category_sales = mysqli_query($conn, $query_category_sales);

    if (!$result_category_sales) {
        echo json_encode(["error" => "Query execution failed: " . mysqli_error($conn)]);
        exit();
    }

    // Prepare category data
    $category_data = [];
    $years = [];
    while ($row = mysqli_fetch_assoc($result_category_sales)) {
        $category_name = $row['category_name'];
        $year = $row['year'];
        $total_sales = (int)$row['total_sales'];

        $category_data[$category_name][$year] = $total_sales;

        if (!in_array($year, $years)) {
            $years[] = $year;
        }
    }

    sort($years);

    // Query to get product sales within categories
    $query_product_sales = "
        SELECT pc.Name AS category_name, p.Name AS product_name, SUM(sod.OrderQty) AS total_sales
        FROM sales_salesorderheader soh
        JOIN sales_salesorderdetail sod ON soh.SalesOrderID = sod.SalesOrderID
        JOIN production_product p ON sod.ProductID = p.ProductID
        JOIN production_productsubcategory psc ON p.ProductSubcategoryID = psc.ProductSubcategoryID
        JOIN production_productcategory pc ON psc.ProductCategoryID = pc.ProductCategoryID
        GROUP BY pc.Name, p.Name
        ORDER BY pc.Name, total_sales DESC;
    ";

    $result_product_sales = mysqli_query($conn, $query_product_sales);

    if (!$result_product_sales) {
        echo json_encode(["error" => "Query execution failed: " . mysqli_error($conn)]);
        exit();
    }

    $product_data = [];
    while ($row = mysqli_fetch_assoc($result_product_sales)) {
        $category_name = $row['category_name'];
        $product_name = $row['product_name'];
        $total_sales = (int)$row['total_sales'];

        $product_data[$category_name][] = ['name' => $product_name, 'sales' => $total_sales];
    }

    echo json_encode([
        "category_data" => $category_data,
        "years" => $years,
        "product_data" => $product_data
    ]);

    mysqli_close($conn);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Terjual Berdasarkan Kategori</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin-bottom: 50px;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
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

        #categorySalesChart, #productSalesChart {
            margin-bottom: 50px;
        }

        .category-select {
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Produk Terjual Berdasarkan Kategori</h1>

    <!-- Dropdown for category selection -->
    <div class="category-select">
        <label for="categorySelect">Select Category:</label>
        <select id="categorySelect">
            <option value="">--Select Category--</option>
        </select>
    </div>

    <!-- Chart for total sales by category -->
    <div id="categorySalesChart"></div>

    <!-- Chart for product sales within a selected category -->
    <div id="productSalesChart"></div>
</div>

<script>
fetch('?fetch_data=true')
    .then(response => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
    })
    .then(data => {
        if (data.error) throw new Error(data.error);

        const { category_data, years, product_data } = data;

        const categorySelect = document.getElementById('categorySelect');

        // Populate dropdown
        Object.keys(category_data).forEach(category => {
            const option = document.createElement('option');
            option.value = category;
            option.textContent = category;
            categorySelect.appendChild(option);
        });

        // Generate initial charts
        const renderCharts = (category) => {
            // Bar chart for total products sold
            Highcharts.chart('categorySalesChart', {
                chart: { type: 'bar' },
                title: { text: 'Total Products Sold by Category and Year' },
                xAxis: { categories: years, title: { text: 'Year' } },
                yAxis: { title: { text: 'Total Products Sold' } },
                series: [{ name: category, data: years.map(y => category_data[category]?.[y] || 0) }]
            });

            // Pie chart for products within selected category
            Highcharts.chart('productSalesChart', {
                chart: { type: 'pie' },
                title: { text: `Product Sales in ${category}` },
                series: [{
                    name: 'Sales',
                    data: (product_data[category] || []).map(p => ({ name: p.name, y: p.sales }))
                }]
            });
        };

        categorySelect.addEventListener('change', () => {
            const selectedCategory = categorySelect.value;
            if (selectedCategory) renderCharts(selectedCategory);
        });

        // Render the first category by default
        const firstCategory = Object.keys(category_data)[0];
        if (firstCategory) renderCharts(firstCategory);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to fetch data. ' + error.message);
    });
</script>
</body>
</html>
