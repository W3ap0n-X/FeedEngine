<?php 
namespace Qck\FeedEngine\Engine;

class PatternWeaver {
    /**
     * Weaves collections based on a specific ratio.
     * * @param array $collections [[Source A], [Source B]]
     * @param array $pattern [2, 1] (Take 2 from A, then 1 from B)
     */
    public static function weave(array $collections, array $pattern): array {
        $result = [];
        $pointers = array_fill(0, count($collections), 0);
        $active = true;

        while ($active) {
            $added_in_round = 0;

            foreach ($pattern as $index => $take_count) {
                if (!isset($collections[$index])) continue;

                // Take N items from this specific collection
                for ($i = 0; $i < $take_count; $i++) {
                    $current_ptr = $pointers[$index];
                    
                    if (isset($collections[$index][$current_ptr])) {
                        $result[] = $collections[$index][$current_ptr];
                        $pointers[$index]++;
                        $added_in_round++;
                    }
                }
            }

            // If we went through the whole pattern and added nothing, we are empty
            if ($added_in_round === 0) {
                $active = false;
            }
        }

        return $result;
    }
}