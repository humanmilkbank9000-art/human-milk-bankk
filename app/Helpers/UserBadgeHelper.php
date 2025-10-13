<?php

namespace App\Helpers;

class UserBadgeHelper
{
    /**
     * Badge definitions with conditions and display properties
     */
    private static $badges = [
        // ========== TOTAL DONATIONS CARD BADGES ==========
        'champion_donor' => [
            'name' => '👑 Champion Donor',
            'icon' => '👑',
            'color' => '#FFD700', // Gold
            'bgGradient' => 'linear-gradient(135deg, #FFD700 0%, #FFA500 100%)',
            'message' => 'A leader in giving',
            'condition' => 'totalDonations >= 15',
            'cardType' => 'donations',
            'priority' => 5,
        ],
        'compassionate_heart' => [
            'name' => '🌿 Compassionate Heart',
            'icon' => '🌿',
            'color' => '#32CD32', // Lime Green
            'bgGradient' => 'linear-gradient(135deg, #32CD32 0%, #228B22 100%)',
            'message' => 'Inspiring kindness',
            'condition' => 'totalDonations >= 10',
            'cardType' => 'donations',
            'priority' => 4,
        ],
        'consistent_donor' => [
            'name' => '⭐ Consistent Donor',
            'icon' => '⭐',
            'color' => '#4169E1', // Royal Blue
            'bgGradient' => 'linear-gradient(135deg, #4169E1 0%, #1E90FF 100%)',
            'message' => 'Steady support',
            'condition' => 'totalDonations >= 5',
            'cardType' => 'donations',
            'priority' => 3,
        ],
        'hope_giver' => [
            'name' => '🌸 Hope Giver',
            'icon' => '🌸',
            'color' => '#FF69B4', // Hot Pink
            'bgGradient' => 'linear-gradient(135deg, #FF69B4 0%, #FF1493 100%)',
            'message' => 'You give hope to families',
            'condition' => 'totalDonations >= 2',
            'cardType' => 'donations',
            'priority' => 2,
        ],
        'first_donation' => [
            'name' => '🩷 First Donation',
            'icon' => '🩷',
            'color' => '#FFC0CB', // Pink
            'bgGradient' => 'linear-gradient(135deg, #FFC0CB 0%, #FFB6C1 100%)',
            'message' => 'Your generosity begins',
            'condition' => 'totalDonations >= 1',
            'cardType' => 'donations',
            'priority' => 1,
        ],

        // ========== TOTAL VOLUME DONATED CARD BADGES ==========
        'gold_heart_donor' => [
            'name' => '🔆 Gold Heart Donor',
            'icon' => '🔆',
            'color' => '#FFD700', // Gold
            'bgGradient' => 'linear-gradient(135deg, #FFD700 0%, #FFA500 100%)',
            'message' => 'Elite contribution',
            'condition' => 'totalVolumeDonated >= 10000',
            'cardType' => 'volume',
            'priority' => 5,
        ],
        'guardian_giver' => [
            'name' => '🪽 Guardian Giver',
            'icon' => '🪽',
            'color' => '#9370DB', // Medium Purple
            'bgGradient' => 'linear-gradient(135deg, #9370DB 0%, #8A2BE2 100%)',
            'message' => 'High impact donor',
            'condition' => 'totalVolumeDonated >= 5000',
            'cardType' => 'volume',
            'priority' => 4,
        ],
        'nourisher' => [
            'name' => '💗 Nourisher',
            'icon' => '💗',
            'color' => '#FF1493', // Deep Pink
            'bgGradient' => 'linear-gradient(135deg, #FF1493 0%, #C71585 100%)',
            'message' => 'Sustaining life',
            'condition' => 'totalVolumeDonated >= 3000',
            'cardType' => 'volume',
            'priority' => 3,
        ],
        'life_flow' => [
            'name' => '💦 Life Flow',
            'icon' => '💦',
            'color' => '#00BFFF', // Deep Sky Blue
            'bgGradient' => 'linear-gradient(135deg, #00BFFF 0%, #1E90FF 100%)',
            'message' => 'Continuously giving',
            'condition' => 'totalVolumeDonated >= 1500',
            'cardType' => 'volume',
            'priority' => 2,
        ],
        'milk_giver' => [
            'name' => '💧 Milk Giver',
            'icon' => '💧',
            'color' => '#87CEEB', // Sky Blue
            'bgGradient' => 'linear-gradient(135deg, #87CEEB 0%, #4682B4 100%)',
            'message' => 'First major milestone',
            'condition' => 'totalVolumeDonated >= 500',
            'cardType' => 'volume',
            'priority' => 1,
        ],

        // ========== INFANTS HELPED CARD BADGES ==========
        'life_guardian' => [
            'name' => '🌎 Life Guardian',
            'icon' => '🌎',
            'color' => '#228B22', // Forest Green
            'bgGradient' => 'linear-gradient(135deg, #228B22 0%, #006400 100%)',
            'message' => 'A hero to many',
            'condition' => 'infantsHelped >= 13',
            'cardType' => 'infants',
            'priority' => 5,
        ],
        'miracle_giver' => [
            'name' => '🪽 Miracle Giver',
            'icon' => '🪽',
            'color' => '#9370DB', // Medium Purple
            'bgGradient' => 'linear-gradient(135deg, #9370DB 0%, #8A2BE2 100%)',
            'message' => 'Changing lives',
            'condition' => 'infantsHelped >= 8',
            'cardType' => 'infants',
            'priority' => 4,
        ],
        'lifesaver' => [
            'name' => '🛡️ Lifesaver',
            'icon' => '🛡️',
            'color' => '#DC143C', // Crimson
            'bgGradient' => 'linear-gradient(135deg, #DC143C 0%, #B22222 100%)',
            'message' => 'Serious impact',
            'condition' => 'infantsHelped >= 5',
            'cardType' => 'infants',
            'priority' => 3,
        ],
        'hope_bringer' => [
            'name' => '🤱 Hope Bringer',
            'icon' => '🤱',
            'color' => '#FF69B4', // Hot Pink
            'bgGradient' => 'linear-gradient(135deg, #FF69B4 0%, #FF1493 100%)',
            'message' => 'Making a difference',
            'condition' => 'infantsHelped >= 2',
            'cardType' => 'infants',
            'priority' => 2,
        ],
        'tiny_hero' => [
            'name' => '👶 Tiny Hero',
            'icon' => '👶',
            'color' => '#FFB6C1', // Light Pink
            'bgGradient' => 'linear-gradient(135deg, #FFB6C1 0%, #FFC0CB 100%)',
            'message' => 'First life touched',
            'condition' => 'infantsHelped >= 1',
            'cardType' => 'infants',
            'priority' => 1,
        ],
    ];

    /**
     * Get all badges with their earned status
     * 
     * @param array $stats User statistics ['totalDonations', 'totalVolumeDonated', 'infantsHelped']
     * @return array Array of badges with earned status
     */
    public static function getBadges(array $stats)
    {
        $result = [];
        
        foreach (self::$badges as $key => $badge) {
            $earned = self::evaluateCondition($badge['condition'], $stats);
            
            $result[$key] = array_merge($badge, [
                'earned' => $earned,
                'key' => $key,
            ]);
        }
        
        return $result;
    }

    /**
     * Get only earned badges
     * 
     * @param array $stats User statistics
     * @return array Array of earned badges only
     */
    public static function getEarnedBadges(array $stats)
    {
        return array_filter(self::getBadges($stats), function($badge) {
            return $badge['earned'];
        });
    }

    /**
     * Get badge to display on a specific card
     * 
     * @param string $cardType Type of card ('donations', 'volume', 'infants')
     * @param array $stats User statistics
     * @return array|null Badge data or null if no badge applies
     */
    public static function getBadgeForCard(string $cardType, array $stats)
    {
        $earnedBadges = self::getEarnedBadges($stats);
        
        // Filter badges by card type
        $cardBadges = array_filter($earnedBadges, function($badge) use ($cardType) {
            return isset($badge['cardType']) && $badge['cardType'] === $cardType;
        });
        
        if (empty($cardBadges)) {
            return null;
        }
        
        // Sort by priority (highest first) and return the highest priority badge
        usort($cardBadges, function($a, $b) {
            $priorityA = $a['priority'] ?? 0;
            $priorityB = $b['priority'] ?? 0;
            return $priorityB - $priorityA; // Descending order
        });
        
        return reset($cardBadges); // Return the first (highest priority) badge
    }

    /**
     * Evaluate a badge condition against user stats
     * 
     * @param string $condition Condition string (e.g., 'totalDonations >= 2')
     * @param array $stats User statistics
     * @return bool Whether condition is met
     */
    private static function evaluateCondition(string $condition, array $stats)
    {
        // Parse condition (simple comparison)
        preg_match('/(\w+)\s*(>=|<=|>|<|==)\s*(\d+)/', $condition, $matches);
        
        if (count($matches) !== 4) {
            return false;
        }
        
        $variable = $matches[1];
        $operator = $matches[2];
        $value = (int) $matches[3];
        
        if (!isset($stats[$variable])) {
            return false;
        }
        
        $actualValue = (int) $stats[$variable];
        
        switch ($operator) {
            case '>=':
                return $actualValue >= $value;
            case '<=':
                return $actualValue <= $value;
            case '>':
                return $actualValue > $value;
            case '<':
                return $actualValue < $value;
            case '==':
                return $actualValue == $value;
            default:
                return false;
        }
    }

    /**
     * Get inspirational message for current progress
     * 
     * @param array $stats User statistics
     * @return string Motivational message
     */
    public static function getMotivationalMessage(array $stats)
    {
        $totalDonations = $stats['totalDonations'] ?? 0;
        $totalVolume = $stats['totalVolumeDonated'] ?? 0;
        $infantsHelped = $stats['infantsHelped'] ?? 0;
        
        // Champion level messages
        if ($totalDonations >= 15) {
            return '👑 Champion Donor! You are a true leader in giving - thank you for your incredible dedication!';
        } elseif ($infantsHelped >= 13) {
            return '🌎 Life Guardian! You\'re a hero to so many precious lives!';
        } elseif ($totalVolume >= 10000) {
            return '🔆 Gold Heart Donor! Your elite contribution is saving lives every day!';
        }
        
        // High impact messages
        elseif ($totalDonations >= 10) {
            return '🌿 Compassionate Heart! Your inspiring kindness touches countless families!';
        } elseif ($infantsHelped >= 8) {
            return '🪽 Miracle Giver! You\'re changing lives in extraordinary ways!';
        } elseif ($totalVolume >= 5000) {
            return '🪽 Guardian Giver! Your high impact donations are making a huge difference!';
        }
        
        // Consistent donor messages
        elseif ($totalDonations >= 5) {
            return '⭐ Consistent Donor! Your steady support means the world to families in need!';
        } elseif ($infantsHelped >= 5) {
            return '🛡️ Lifesaver! Your serious impact has touched so many precious lives!';
        } elseif ($totalVolume >= 3000) {
            return '💗 Nourisher! You\'re sustaining life with every precious donation!';
        }
        
        // Early journey messages
        elseif ($totalDonations >= 2) {
            return '🌸 Hope Giver! You\'re bringing hope to families who need it most!';
        } elseif ($infantsHelped >= 2) {
            return '🤱 Hope Bringer! You\'re making a real difference in tiny lives!';
        } elseif ($totalVolume >= 1500) {
            return '💦 Life Flow! Your continuous giving is a blessing to many!';
        }
        
        // First milestone messages
        elseif ($totalDonations >= 1) {
            return '🩷 First Donation! Amazing start - your generosity begins a beautiful journey!';
        } elseif ($infantsHelped >= 1) {
            return '👶 Tiny Hero! You\'ve touched your first precious life!';
        } elseif ($totalVolume >= 500) {
            return '💧 Milk Giver! You\'ve reached your first major milestone!';
        }
        
        // No donations yet
        else {
            return '💝 Start your journey of giving life today - every drop makes a difference!';
        }
    }
}
