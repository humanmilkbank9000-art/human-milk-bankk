<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Get all FAQ items grouped by category
     * 
     * @return array
     */
    public static function getFaqItems()
    {
        return [
        

[
                'id' => 1,
                'category' => 'Safety & Assurance',
                'icon' => 'fas fa-shield-alt',
                'color' => '#4facfe', // Baby blue
                'question' => 'Safety and  health screening screening process',
                'answer' => 'Our health screening process includes: (1) Donor health screening questionnaire covering medical history, medications, lifestyle, and basic infant information, (2) Please be honest and true to your responses to ensure proper safety and to know your qualification, (3) Review and submit your health screening, (4) Wait for the admin to verify and accept your health screening(you will be verified through notification).',
                'tags' => ['safety', 'screening', 'health']
            ],

[
                'id' => 2,
                'category' => 'Donation Process',
                'icon' => 'fas fa-hand-holding-heart',
                'color' => '#f093fb', // Soft pink
                'question' => 'How to donate breastmilk?',
                'answer' => 'To donate milk safely: (1) After taking health screening after being accepted and  eligible, (2) You may now request to date in the donate page, (3) Schedule a walk-in donation, or home collection method.',
                'tags' => ['donation', 'getting started', 'process']
            ],
[
                'id' => 3,
                'category' => 'Walk-in Donation',
                'icon' => 'fas fa-info-circle',
                'color' => '#667eea', // Lavender
                'question' => 'How walk-in donation works?',
                'answer' => 'To donate via walk-in, navigate to the "Donate " section: (1) Select your preferred day (highlightened in color pink in the calendar) and place your schedule. (1) Please take note of your selected date and to visit the human milk bank and the unit staff will welcome and assist you',
                'tags' => ['donation', 'process', 'walk-in']
            ],            
[
                'id' => 4,
                'category' => 'Home Collection',
                'icon' => 'fas fa-hand-holding-heart',
                'color' => '#f093fb', // Soft pink
                'question' => 'How home collection donation works?',
                'answer' => 'Navigate to the donate page and select "Home Collection": (1) Allow the page to access your location so that the human milk bank staff will know your map location, (2) You will be notified on what is the date and time. (3) The human milk bank staff visit to collect your donation in the assigned date (4) The human milk bank staff will then approve and aceept your donation.',
                'tags' => ['donation', 'process', 'home collection']
            ],
            [
                'id' => 5,
                'category' => 'Safety & Quality',
                'icon' => 'fas fa-shield-alt',
                'color' => '#4facfe', // Baby blue
                'question' => 'How do I store safely my accumulated breastmilk at home?',
                    'answer' => 'Use clean, sterilized milk containers or milk storage bags. Wash hands before expressing. Label each container with your name, date, and time of expression. Store milk at the back of the freezer (–18°C or lower), not on the freezer door. Do not mix freshly expressed warm milk with already frozen milk. Keep containers upright and avoid overfilling to prevent leaks.',
                'tags' => ['safety', 'guide', 'home collection']
            ],
            [
                'id' => 6,
                'category' => 'Breastmilk Request',
                'icon' => 'fas fa-baby',
                'color' => '#a8edea', // Cream/mint
                'question' => 'How to request breastmilk?',
                'answer' => 'You will need: (1) Scanned or a picture of a medical prescription from a doctor, (2) Place or select your preferred date to visit the unit, (3) Arrive on time, (4) Human milk bank staff will interview you as a guardian, (5) Note: Your request might be rejected; and if so, you might be referred to the other nearest human milk bank',
                'tags' => ['matching', 'infant', 'distribution']
            ],
            [
                'id' => 7,
                'category' => 'The importance of your donation',
                'icon' => 'fas fa-snowflake',
                'color' => '#ffeaa7', // Soft yellow
                'question' => 'Will my donation even in smaller amount make a difference?',
                'answer' => 'Yes. Even small amounts of donated breastmilk can save lives. Many infants in NICUs and pediatric wards depend on donor milk because they are premature, low birth weight, sick, or temporarily unable to receive milk from their mothers due to medical or personal reasons. Donor breastmilk provides vital nutrition and immunity for their survival and recovery.',
                'tags' => ['importance', 'breastmilk', 'health']
            ],
            [
    'id' => 8,
    'category' => 'Donor Support',
    'icon' => 'fas fa-heart',
    'color' => '#ff9ff3', // Soft rose
    'question' => 'Is there a minimum amount required to donate?',
    'answer' => 'No. There is no minimum volume requirement. You may donate any amount of breastmilk as long as it is safely expressed, stored, and within the allowed storage period.',
    'tags' => ['donation', 'amount', 'flexibility']
],

[
    'id' => 9,
    'category' => 'Donation Guidelines',
    'icon' => 'fas fa-box',
    'color' => '#55efc4', // Light mint
    'question' => 'What types of containers are accepted for milk donation?',
    'answer' => 'Only clean, sealed, and BPA-free breastmilk storage bags or sterilized hard plastic containers with lids are accepted. Used feeding bottles, glass containers, or unsealed containers are not allowed for safety reasons.',
    'tags' => ['containers', 'guidelines', 'storage']
],

[
    'id' => 10,
    'category' => 'After Donation',
    'icon' => 'fas fa-clipboard-check',
    'color' => '#74b9ff', // Soft blue
    'question' => 'Will I receive updates after donating?',
    'answer' => 'Yes. You will be notified once your donation is received, screened, and accepted. In some cases, you may also receive a record of how your donation helped infants in need.',
    'tags' => ['updates', 'donor', 'process']
]
        ];
    }

    /**
     * Get FAQ items grouped by category
     * 
     * @return array
     */
    public static function getFaqByCategory()
    {
        $faqItems = self::getFaqItems();
        $grouped = [];

        foreach ($faqItems as $item) {
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
     * Search FAQ items
     * 
     * @param string $query
     * @return array
     */
    public static function searchFaq($query)
    {
        $faqItems = self::getFaqItems();
        $query = strtolower($query);
        
        return array_filter($faqItems, function($item) use ($query) {
            return 
                stripos($item['question'], $query) !== false ||
                stripos($item['answer'], $query) !== false ||
                in_array($query, array_map('strtolower', $item['tags']));
        });
    }
}
