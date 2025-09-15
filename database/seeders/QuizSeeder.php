<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseModule;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizModules = CourseModule::where('type', 'quiz')->get();
        
        if ($quizModules->count() == 0) {
            $this->command->info('No quiz modules found. Please create quiz modules first.');
            return;
        }

        foreach ($quizModules as $module) {
            // Create sample quiz questions for each quiz module
            $questions = [
                [
                    'question' => 'What is the main purpose of ' . $module->title . '?',
                    'explanation' => 'This question tests your understanding of the fundamental concepts covered in this module.',
                    'points' => 2,
                    'answers' => [
                        ['text' => 'To provide basic knowledge', 'is_correct' => true],
                        ['text' => 'To confuse students', 'is_correct' => false],
                        ['text' => 'To waste time', 'is_correct' => false],
                        ['text' => 'To make things complicated', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => 'Which of the following is NOT a key feature of ' . $module->title . '?',
                    'explanation' => 'Understanding what is NOT part of the topic helps clarify the boundaries of the subject.',
                    'points' => 1,
                    'answers' => [
                        ['text' => 'Practical application', 'is_correct' => false],
                        ['text' => 'Theoretical foundation', 'is_correct' => false],
                        ['text' => 'Random guessing', 'is_correct' => true],
                        ['text' => 'Systematic approach', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => 'How many main components are typically involved in ' . $module->title . '?',
                    'explanation' => 'This question tests your knowledge of the structure and components of the topic.',
                    'points' => 1,
                    'answers' => [
                        ['text' => '2-3 components', 'is_correct' => false],
                        ['text' => '4-5 components', 'is_correct' => true],
                        ['text' => '10+ components', 'is_correct' => false],
                        ['text' => 'Only 1 component', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => 'What is the most important skill to develop when learning ' . $module->title . '?',
                    'explanation' => 'Critical thinking is essential for understanding and applying concepts effectively.',
                    'points' => 2,
                    'answers' => [
                        ['text' => 'Memorization', 'is_correct' => false],
                        ['text' => 'Critical thinking', 'is_correct' => true],
                        ['text' => 'Speed reading', 'is_correct' => false],
                        ['text' => 'Copy-pasting', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => 'Which statement best describes the learning outcome of ' . $module->title . '?',
                    'explanation' => 'The learning outcome should focus on practical application and understanding.',
                    'points' => 1,
                    'answers' => [
                        ['text' => 'Students will memorize all facts', 'is_correct' => false],
                        ['text' => 'Students will apply concepts practically', 'is_correct' => true],
                        ['text' => 'Students will pass without effort', 'is_correct' => false],
                        ['text' => 'Students will avoid all challenges', 'is_correct' => false],
                    ]
                ],
            ];

            foreach ($questions as $index => $questionData) {
                // Create question
                $question = QuizQuestion::create([
                    'course_module_id' => $module->id,
                    'question' => $questionData['question'],
                    'explanation' => $questionData['explanation'],
                    'points' => $questionData['points'],
                    'order_no' => $index + 1,
                ]);

                // Create answers
                foreach ($questionData['answers'] as $answerIndex => $answerData) {
                    QuizAnswer::create([
                        'quiz_question_id' => $question->id,
                        'answer_text' => $answerData['text'],
                        'is_correct' => $answerData['is_correct'],
                        'order_no' => $answerIndex + 1,
                    ]);
                }
            }
        }

        $this->command->info('Sample quiz questions created successfully!');
    }
}