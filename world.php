<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8');


$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    echo "Database connection error.";
    exit;
}

$country = isset($_GET['country']) ? trim($_GET['country']) : '';
$lookup = isset($_GET['lookup']) ? $_GET['lookup'] : 'countries';

$sql = empty($country)
    ? ($lookup === 'cities'
        ? "SELECT name, district, population FROM cities"
        : "SELECT name, continent, independence_year, head_of_state FROM countries")
    : ($lookup === 'cities'
        ? "SELECT cities.name, cities.district, cities.population 
           FROM cities 
           INNER JOIN countries ON cities.country_code = countries.code 
           WHERE countries.name LIKE :country"
        : "SELECT name, continent, independence_year, head_of_state 
           FROM countries 
           WHERE name LIKE :country");

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute(empty($country) ? [] : ['country' => "%$country%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Query failed: " . $e->getMessage());
    echo "Query execution error.";
    exit;
}

if (empty($results)) {
    echo "<p>No results found.</p>";
    exit;
}


?>
<table>
    <?php if ($lookup === 'cities'): ?>
        <thead>
            <tr>
                <th>Name</th>
                <th>District</th>
                <th>Population</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['district']); ?></td>
                <td><?= number_format($row['population']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    <?php else: ?>
        <thead>
            <tr>
                <th>Name</th>
                <th>Continent</th>
                <th>Independence Year</th>
                <th>Head of State</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['continent']); ?></td>
                <td><?= $row['independence_year'] ?? 'N/A'; ?></td>
                <td><?= htmlspecialchars($row['head_of_state'] ?? 'N/A'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    <?php endif; ?>
</table>
