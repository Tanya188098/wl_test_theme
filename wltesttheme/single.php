<?php get_header();?>
<?php
$cars = get_posts([
    'numberposts' => 5,
    'category'    => 0,
    'orderby'     => 'date',
    'order'       => 'DESC',
    'post_type'   => 'car',
]);
?>

    <h1><?= get_the_title(); ?></h1>
    <p><?= get_the_content(); ?></p>
    <?= get_the_post_thumbnail() ?>

    <ul>
        <li>Марка:
            <?php
            foreach(get_the_terms($cars->ID,'brands') as $brand){
                echo $brand->name;
            }?>
        </li>
        <li>Страна производитель:
            <?php
            foreach(get_the_terms($cars->ID,'countries') as $country){
                echo $country->name;
            }?>

        <li>Цвет:
            <?php
            foreach(get_post_custom_values('color') as $color){?>
                <input type="color" value="<?= $color?>">
            <?php }
        ?></li>

        <li>Топливо: <?php
            foreach(get_post_custom_values('select') as $fuel){
                echo $fuel;
            }?>
        </li>
        <li>Мощность: <?php
            foreach(get_post_custom_values('power') as $power){
                echo $power;
            }
         ?>
        </li>
        <li>Цена:<?php
            foreach(get_post_custom_values('price') as $price){
                echo $price;
            }
            ?></li>
    </ul>

<?php get_footer();?>