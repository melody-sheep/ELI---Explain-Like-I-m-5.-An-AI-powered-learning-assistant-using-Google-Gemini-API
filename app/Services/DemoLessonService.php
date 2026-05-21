<?php

namespace App\Services;

class DemoLessonService
{
    /**
     * Get all demo lessons (hardcoded, no API calls)
     */
    public static function getAllLessons()
    {
        return [
            (object) [
                'id' => 1,
                'title' => 'Introduction to Artificial Intelligence',
                'description' => 'Learn the fundamentals of AI, machine learning, and neural networks. Perfect for beginners.',
                'subject' => 'Computer Science',
                'contents' => [
                    (object) [
                        'id' => 1,
                        'title' => 'What is Artificial Intelligence?',
                        'content_type' => 'text',
                        'content' => 'Artificial Intelligence (AI) is the simulation of human intelligence in machines that are programmed to think and learn. Key concepts include:
                        
• Machine Learning: Algorithms that learn from data
• Deep Learning: Neural networks with multiple layers
• Natural Language Processing: Understanding human language
• Computer Vision: Interpreting visual information

Example applications: Self-driving cars, virtual assistants (Siri/Alexa), recommendation systems (Netflix/Amazon).'
                    ],
                    (object) [
                        'id' => 2,
                        'title' => 'Types of AI Systems',
                        'content_type' => 'text',
                        'content' => 'There are 3 main types of AI:
                        
1. ANI (Artificial Narrow Intelligence) - Designed for specific tasks
   - Chess-playing AI
   - Facial recognition
   - Spam filters

2. AGI (Artificial General Intelligence) - Human-like intelligence
   - Currently theoretical
   - Can learn any intellectual task

3. ASI (Artificial Superintelligence) - Exceeds human intelligence
   - Hypothetical future AI
   - Could solve complex global problems'
                    ],
                    (object) [
                        'id' => 3,
                        'title' => 'Watch: Introduction to Neural Networks',
                        'content_type' => 'video',
                        'content' => 'https://www.youtube.com/watch?v=aircAruvnKk'
                    ]
                ]
            ],
            (object) [
                'id' => 2,
                'title' => 'Laravel 11 Essentials',
                'description' => 'Master Laravel PHP framework - routing, controllers, Eloquent ORM, and Blade templating.',
                'subject' => 'Web Development',
                'contents' => [
                    (object) [
                        'id' => 1,
                        'title' => 'Laravel Routing Fundamentals',
                        'content_type' => 'text',
                        'content' => 'Routes in Laravel define your application URLs:

Basic Route:
Route::get(\'/hello\', function() {
    return \'Hello World\';
});

Route Parameters:
Route::get(\'/user/{id}\', function($id) {
    return \'User \'.$id;
});

Named Routes:
Route::get(\'/profile\', function() {
    //
})->name(\'profile\');

Route Groups:
Route::middleware(\'auth\')->group(function() {
    Route::get(\'/dashboard\', function() {
        //
    });
});'
                    ],
                    (object) [
                        'id' => 2,
                        'title' => 'Eloquent ORM Basics',
                        'content_type' => 'text',
                        'content' => 'Eloquent is Laravel\'s ORM for database interaction:

Model Example:
php artisan make:model Post

Basic Queries:
// Get all records
$posts = Post::all();

// Find by ID
$post = Post::find(1);

// Create new record
$post = new Post();
$post->title = \'My Title\';
$post->save();

// Update record
$post = Post::find(1);
$post->title = \'New Title\';
$post->save();

// Delete record
$post = Post::find(1);
$post->delete();

Relationships:
class User extends Model {
    public function posts() {
        return $this->hasMany(Post::class);
    }
}'
                    ],
                    (object) [
                        'id' => 3,
                        'title' => 'Laravel Documentation',
                        'content_type' => 'link',
                        'content' => 'https://laravel.com/docs/11.x'
                    ]
                ]
            ],
            (object) [
                'id' => 3,
                'title' => 'Database Design Patterns',
                'description' => 'Learn normalization, indexing, relationships, and best practices for SQL databases.',
                'subject' => 'Database',
                'contents' => [
                    (object) [
                        'id' => 1,
                        'title' => 'Database Normalization',
                        'content_type' => 'text',
                        'content' => 'Normalization eliminates data redundancy:

1NF (First Normal Form):
- Atomic values (no repeating groups)
- Each column has single value

2NF (Second Normal Form):
- Must be in 1NF
- No partial dependencies

3NF (Third Normal Form):
- Must be in 2NF
- No transitive dependencies

Example - Unnormalized:
StudentID, Name, Course1, Course2, Course3

Normalized:
Table1: Students(StudentID, Name)
Table2: Enrollments(StudentID, CourseID)
Table3: Courses(CourseID, Title)'
                    ],
                    (object) [
                        'id' => 2,
                        'title' => 'SQL Indexing Strategies',
                        'content_type' => 'text',
                        'content' => 'Indexes speed up SELECT queries but slow INSERT/UPDATE:

CREATE INDEX idx_email ON users(email);

Types of Indexes:
1. B-Tree - Most common, good for equality/range
2. Hash - Fast equality lookups
3. Full-text - Text searching

When to Index:
✓ Primary keys (automatically indexed)
✓ Foreign keys
✓ Columns in WHERE clauses
✓ Columns in JOIN conditions

When NOT to Index:
✗ Small tables
✗ Columns with many NULLs
✗ Frequently updated columns
✗ Columns with low cardinality (few unique values)'
                    ],
                    (object) [
                        'id' => 3,
                        'title' => 'Database Relationships PDF',
                        'content_type' => 'file',
                        'content' => 'sample-database-guide.pdf'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get a single demo lesson by ID
     */
    public static function getLesson($id)
    {
        $lessons = self::getAllLessons();
        foreach ($lessons as $lesson) {
            if ($lesson->id == $id) {
                return $lesson;
            }
        }
        return null;
    }
}