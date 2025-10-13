<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NutritionalGuideController extends Controller
{
    /**
     * Get all Nutritional Guide items
     * 
     * @return array
     */
    public static function getNutritionalGuideItems()
    {
        return [
            [
                'id' => 1,
                'category' => 'Benefits for Infants',
                'icon' => 'fas fa-baby',
                'color' => '#a8edea', // Soft mint
                'title' => 'Benefits of Human Breastmilk for Infants',
                'content' => 'Human breastmilk is nature\'s perfect food for babies. It provides complete nutrition with the ideal balance of proteins, fats, vitamins, and carbohydrates. Breastmilk contains antibodies that help protect infants from infections, reduces the risk of allergies and asthma, promotes healthy brain development, and strengthens the immune system. It\'s easily digestible and adapts to your baby\'s changing needs as they grow.',
                'tips' => [
                    'Contains antibodies for immune protection',
                    'Perfect nutritional balance for growth',
                    'Reduces risk of infections and allergies',
                    'Promotes brain and cognitive development',
                    'Easily digestible for baby\'s system'
                ],
                'tags' => ['infants', 'nutrition', 'health', 'immunity']
            ],
            [
                'id' => 2,
                'category' => 'Benefits for Mothers',
                'icon' => 'fas fa-heart',
                'color' => '#f093fb', // Soft pink
                'title' => 'Benefits of Breastfeeding for Mothers',
                'content' => 'Breastfeeding offers tremendous benefits for mothers too! It helps the uterus return to its pre-pregnancy size faster, burns extra calories aiding postpartum weight loss, releases oxytocin which promotes bonding and reduces stress, lowers the risk of breast and ovarian cancers, and may reduce the risk of type 2 diabetes. It\'s also convenient, cost-effective, and creates special bonding moments with your baby.',
                'tips' => [
                    'Helps uterus contract and recover faster',
                    'Burns 300-500 extra calories per day',
                    'Reduces risk of breast and ovarian cancer',
                    'Promotes mother-baby bonding through oxytocin',
                    'Saves money and is always available'
                ],
                'tags' => ['mothers', 'health', 'bonding', 'postpartum']
            ],
            [
                'id' => 3,
                'category' => 'Best Practices',
                'icon' => 'fas fa-check-circle',
                'color' => '#4facfe', // Baby blue
                'title' => 'Breastfeeding Best Practices',
                'content' => 'Successful breastfeeding starts with proper positioning and latch. Feed on demand, typically 8-12 times in 24 hours for newborns. Ensure baby\'s mouth covers the entire areola, not just the nipple. Watch for hunger cues like rooting and hand-to-mouth movements. Empty one breast before switching to the other. Create a comfortable, quiet environment. Seek help from lactation consultants if you experience difficulties.',
                'tips' => [
                    'Feed on demand (8-12 times daily for newborns)',
                    'Ensure proper latch covering the areola',
                    'Watch for hunger cues, not just crying',
                    'Empty one breast before switching',
                    'Stay comfortable and relaxed during feeding'
                ],
                'tags' => ['breastfeeding', 'techniques', 'latch', 'positioning']
            ],
            [
                'id' => 4,
                'category' => 'Nutrition for Mothers',
                'icon' => 'fas fa-utensils',
                'color' => '#ffeaa7', // Soft yellow
                'title' => 'Nutritional Tips for Lactating Mothers',
                'content' => 'Your body needs extra nutrition to produce quality milk. Consume an additional 300-500 calories per day from nutrient-dense foods. Focus on protein-rich foods (lean meats, eggs, legumes), calcium sources (dairy, leafy greens), whole grains, and healthy fats. Take prenatal vitamins as recommended. Avoid processed foods, excessive caffeine (limit to 2-3 cups), and alcohol. Your diet directly affects milk quality and quantity.',
                'tips' => [
                    'Add 300-500 extra calories from healthy sources',
                    'Eat protein-rich foods (meat, eggs, beans, nuts)',
                    'Include calcium sources (dairy, leafy greens)',
                    'Take recommended vitamins and supplements',
                    'Limit caffeine and avoid alcohol'
                ],
                'tags' => ['nutrition', 'diet', 'calories', 'vitamins']
            ],
            [
                'id' => 5,
                'category' => 'Lactation Foods',
                'icon' => 'fas fa-apple-alt',
                'color' => '#a8edea', // Soft mint
                'title' => 'Best Foods That Support Milk Production',
                'content' => 'Certain foods are known as galactagogues - they help boost milk supply. Include oatmeal (rich in iron), leafy greens (spinach, kale), nuts and seeds (almonds, flaxseed), lean proteins (chicken, fish, eggs), legumes (lentils, chickpeas), whole grains (brown rice, quinoa), and lactation-boosting herbs like fenugreek. Sweet potatoes, avocados, and salmon are also excellent choices. Remember, consistent feeding and hydration matter most!',
                'tips' => [
                    'Oatmeal - rich in iron, boosts supply',
                    'Leafy greens - spinach, kale, moringa',
                    'Nuts & seeds - almonds, cashews, flaxseed',
                    'Lean proteins - chicken, fish, eggs, tofu',
                    'Whole grains - brown rice, quinoa, barley'
                ],
                'tags' => ['lactation', 'foods', 'milk supply', 'galactagogues']
            ],
            [
                'id' => 6,
                'category' => 'Hydration & Rest',
                'icon' => 'fas fa-bed',
                'color' => '#f093fb', // Soft pink
                'title' => 'Hydration and Rest Importance',
                'content' => 'Adequate hydration is crucial for milk production. Drink at least 8-10 glasses of water daily, or more if you feel thirsty. Keep water nearby during nursing sessions. Rest is equally important - lack of sleep can reduce milk supply and affect your overall health. Sleep when baby sleeps, accept help from family, and don\'t hesitate to ask for support. Your body needs rest to recover and produce milk efficiently.',
                'tips' => [
                    'Drink 8-10 glasses of water daily minimum',
                    'Keep water bottle handy during nursing',
                    'Sleep when your baby sleeps',
                    'Accept help with household tasks',
                    'Prioritize rest for milk production'
                ],
                'tags' => ['hydration', 'rest', 'sleep', 'self-care']
            ],
            [
                'id' => 7,
                'category' => 'Stress Management',
                'icon' => 'fas fa-spa',
                'color' => '#4facfe', // Baby blue
                'title' => 'How Stress Affects Milk Supply',
                'content' => 'Chronic stress and anxiety can interfere with your let-down reflex and reduce milk production. Stress hormones like cortisol can temporarily decrease oxytocin release, which is essential for milk flow. Practice relaxation techniques: deep breathing, meditation, gentle yoga, or listening to calming music. Create a peaceful nursing environment. Talk about your feelings with supportive friends or family. Remember, it\'s okay to ask for help and take time for yourself.',
                'tips' => [
                    'Stress hormones can reduce milk let-down',
                    'Practice deep breathing and meditation',
                    'Create calm, quiet nursing environment',
                    'Talk about feelings with support network',
                    'Self-care is essential for milk production'
                ],
                'tags' => ['stress', 'mental health', 'relaxation', 'oxytocin']
            ],
            [
                'id' => 8,
                'category' => 'Pumping & Storage',
                'icon' => 'fas fa-fill-drip',
                'color' => '#ffeaa7', // Soft yellow
                'title' => 'Safe Pumping and Storage Practices',
                'content' => 'Clean pump parts thoroughly after each use with hot soapy water. Pump regularly to maintain supply - aim for every 2-3 hours if exclusively pumping. Store milk in clean, sterilized containers or breastmilk bags. Label with date and time. Fresh milk: room temp (4 hours), refrigerator (4 days), freezer (6 months). Thaw frozen milk in refrigerator overnight or under warm running water. Never microwave! Use oldest milk first. Don\'t refreeze thawed milk.',
                'tips' => [
                    'Sterilize pump parts before first use',
                    'Clean all parts with hot soapy water after use',
                    'Pump every 2-3 hours for supply maintenance',
                    'Label all milk with date and time',
                    'Follow safe storage times and temperatures'
                ],
                'tags' => ['pumping', 'storage', 'safety', 'hygiene']
            ],
            [
                'id' => 9,
                'category' => 'Myths vs Facts',
                'icon' => 'fas fa-lightbulb',
                'color' => '#a8edea', // Soft mint
                'title' => 'Lactation Myths vs Facts',
                'content' => '**MYTH:** Small breasts produce less milk. **FACT:** Breast size doesn\'t affect milk production. **MYTH:** You must drink milk to make milk. **FACT:** Dairy isn\'t necessary; stay hydrated with any fluids. **MYTH:** Breastfeeding is painful. **FACT:** Proper latch should not hurt; seek help if painful. **MYTH:** Formula is just as good. **FACT:** Breastmilk contains antibodies and adapts to baby\'s needs. **MYTH:** Pump output shows supply. **FACT:** Babies are more efficient than pumps.',
                'tips' => [
                    'Breast size doesn\'t determine milk supply',
                    'Dairy consumption is not required',
                    'Pain indicates improper latch - seek help',
                    'Breastmilk adapts uniquely to your baby',
                    'Babies extract milk better than pumps'
                ],
                'tags' => ['myths', 'facts', 'education', 'misconceptions']
            ],
            [
                'id' => 10,
                'category' => 'Empowerment',
                'icon' => 'fas fa-star',
                'color' => '#f093fb', // Soft pink
                'title' => 'You Are Doing Amazing!',
                'content' => 'Every drop of breastmilk you provide is precious! Whether you breastfeed exclusively, pump, or supplement with formula - you are nourishing your baby and that\'s what matters. Breastfeeding is a learned skill for both you and baby; challenges are normal and temporary. Trust your body - it knows how to feed your baby. Seek support when needed, celebrate small victories, and be kind to yourself. Your dedication and love are what truly nourish your little one.',
                'tips' => [
                    'Every feeding journey is unique and valid',
                    'Fed is best - your baby is loved and cared for',
                    'Challenges are temporary, seek support',
                    'Trust your body and maternal instincts',
                    'You are stronger than you know!'
                ],
                'tags' => ['empowerment', 'support', 'motivation', 'self-love']
            ],
            [
                'id' => 11,
                'category' => 'Nutrition for Mothers',
                'icon' => 'fas fa-heartbeat',
                'color' => '#4facfe', // Baby blue
                'title' => 'Vitamins and Supplements for Nursing Mothers',
                'content' => 'Continue taking prenatal vitamins while breastfeeding. Key nutrients include: Vitamin D (for bone health), Omega-3 fatty acids (brain development), Vitamin B12 (if vegetarian/vegan), Iron (if levels are low), Calcium (1000mg daily), and Iodine. Vitamin A, C, and folate are also important. Always consult your healthcare provider before starting new supplements. A balanced diet usually provides most nutrients, but supplements ensure you and baby get everything needed.',
                'tips' => [
                    'Continue prenatal vitamins while nursing',
                    'Ensure adequate Vitamin D intake',
                    'Include Omega-3 for baby\'s brain development',
                    'Vegetarians: supplement Vitamin B12',
                    'Consult doctor before new supplements'
                ],
                'tags' => ['vitamins', 'supplements', 'nutrition', 'health']
            ],
            [
                'id' => 12,
                'category' => 'Best Practices',
                'icon' => 'fas fa-clock',
                'color' => '#ffeaa7', // Soft yellow
                'title' => 'Understanding Feeding Schedules and Cluster Feeding',
                'content' => 'Newborns feed frequently and unpredictably - this is normal! Cluster feeding (multiple feeds close together) often occurs in evenings and during growth spurts. Don\'t watch the clock; follow your baby\'s hunger cues. Frequent feeding establishes supply and is not a sign of low milk production. As babies grow, they\'ll naturally space out feedings. Growth spurts typically occur at 2-3 weeks, 6 weeks, 3 months, and 6 months. During these times, feed more often to boost supply.',
                'tips' => [
                    'Newborns feed 8-12+ times daily',
                    'Cluster feeding is normal, especially evenings',
                    'Follow baby\'s cues, not the clock',
                    'Growth spurts increase feeding frequency',
                    'Frequent feeding boosts milk supply'
                ],
                'tags' => ['schedule', 'cluster feeding', 'growth spurts', 'frequency']
            ]
        ];
    }

    /**
     * Get nutritional guide items grouped by category
     * 
     * @return array
     */
    public static function getGuideByCategory()
    {
        $items = self::getNutritionalGuideItems();
        $grouped = [];

        foreach ($items as $item) {
            $category = $item['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [
                    'category' => $category,
                    'icon' => $item['icon'],
                    'color' => $item['color'],
                    'items' => []
                ];
            }
            $grouped[$category]['items'][] = $item;
        }

        return array_values($grouped);
    }

    /**
     * Search nutritional guide items
     * 
     * @param string $query
     * @return array
     */
    public static function searchGuide($query)
    {
        $items = self::getNutritionalGuideItems();
        $query = strtolower($query);
        
        return array_filter($items, function($item) use ($query) {
            return 
                stripos($item['title'], $query) !== false ||
                stripos($item['content'], $query) !== false ||
                in_array($query, array_map('strtolower', $item['tags']));
        });
    }
}
