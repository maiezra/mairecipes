<?php
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function calculate_matching_percentage($recipe, $user) {
    $weights = [
        'dietary_preferences' => 25,
        'allergies' => 20,
        'cooking_skill' => 15,
        'cooking_frequency' => 10,
        'favorite_cuisine' => 10,
        'dietary_restrictions' => 10,
        'meal_preferences' => 10,
        'meal_type' => 10,
        'cooking_time' => 10,
    ];

    $matching = 0;

    if (isset($user['dietary_preferences']) && $recipe['dietary_preferences'] === $user['dietary_preferences']) {
        $matching += $weights['dietary_preferences'];
    }

    if (isset($user['allergies']) && !has_allergies($recipe['ingredients'], $user['allergies'])) {
        $matching += $weights['allergies'];
    } else {
        $matching -= $weights['allergies'];
    }

    if (isset($user['cooking_skill']) && $recipe['cooking_skill'] === $user['cooking_skill']) {
        $matching += $weights['cooking_skill'];
    }

    // Проверяем, существуют ли индексы cooking_frequency и meal_preferences
    if (isset($user['cooking_frequency']) && isset($recipe['cooking_frequency']) && $recipe['cooking_frequency'] === $user['cooking_frequency']) {
        $matching += $weights['cooking_frequency'];
    }

    if (isset($user['favorite_cuisine']) && $recipe['favorite_cuisine'] === $user['favorite_cuisine']) {
        $matching += $weights['favorite_cuisine'];
    }

    if (isset($user['dietary_restrictions']) && $recipe['dietary_restrictions'] === $user['dietary_restrictions']) {
        $matching += $weights['dietary_restrictions'];
    }

    if (isset($user['meal_preferences']) && isset($recipe['meal_preferences']) && $recipe['meal_preferences'] === $user['meal_preferences']) {
        $matching += $weights['meal_preferences'];
    }

    if (isset($recipe['meal_type']) && isset($user['meal_type']) && $recipe['meal_type'] === $user['meal_type']) {
        $matching += $weights['meal_type'];
    }

    if (isset($user['max_cooking_time']) && isset($recipe['cooking_time']) && $recipe['cooking_time'] <= $user['max_cooking_time']) {
        $matching += $weights['cooking_time'];
    } else {
        $matching -= $weights['cooking_time'];
    }

    return $matching;
}

function has_allergies($ingredients, $allergies) {
    foreach (explode(',', $allergies) as $allergy) {
        if (stripos($ingredients, trim($allergy)) !== false) {
            return true;
        }
    }
    return false;
}
?>
