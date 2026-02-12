<h1><?= $product['name'] ?></h1>

<p><?= $product['description'] ?></p>

<p>
    Price: <?= $product['price_current'] ?>$
    <del><?= $product['price_old'] ?>$</del>
</p>

<h3>Colors</h3>
<ul>
    <?php foreach ($colors as $c): ?>
    <li><?= $c ?></li>
    <?php endforeach; ?>
</ul>

<h3>Sizes</h3>
<ul>
    <?php foreach ($size as $s): ?>
        <li><?= $s ?></li>
    <?php endforeach; ?>
</ul>