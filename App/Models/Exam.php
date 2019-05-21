<?php
namespace App\Models;

use Core\Model;
use \PDO;

class Exam extends Model
{
    public static function all()
    {
        try{
            $db=static::getDB();
            $sql="SELECT exams.id,exams.study_field_id,exams.name,exams.is_disabled,exams.question_count,exams.has_negative_mark,exams.negative_mark_count,exams.exam_time,exams.exam_mark,exams.exam_coefficient,exams.create_date_persian,study_fields.title AS study_field FROM exams INNER JOIN study_fields ON study_fields.id = exams.study_field_id";
            $result=$db->query($sql);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC):[];
        }catch (\Exception $e){
            return [];
        }
    }

    public static function by_id($exam_id)
    {
        try{
            $db=static::getDB();
            $sql="SELECT exams.id,exams.study_field_id,exams.name,exams.is_disabled,exams.question_count,exams.has_negative_mark,exams.negative_mark_count,exams.exam_time,exams.exam_mark,exams.exam_coefficient,exams.create_date_persian,study_fields.title AS study_field FROM exams INNER JOIN study_fields ON study_fields.id = exams.study_field_id WHERE exams.id = :exam_id";
            $stmt=$db->prepare($sql);
            $stmt->bindParam(":exam_id",$exam_id);
            return $stmt->execute() ? $stmt->fetch(PDO::FETCH_ASSOC):[];
        }catch (\Exception $e){
            return [];
        }
    }

    public static function get_questions_by_exam_id($examId)
    {
        try{
         return static::select("questions","*",['exam_id'=>$examId]);
        }catch (\Exception $e){
            return [];
        }
    }

    public static function create_logs($userId,$answers)
    {
        try{
            foreach ($answers as $question=>$answer){
                $questionId=explode("-",$question)[1];
                static::insert("exam_logs",[
                   'user_id'=>$userId,
                   'question_id'=>$questionId,
                    'choice'=>$answer
                ]);
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function calculate_and_save_exam_result($userId, $examId, $answers)
    {
        try{
            $examResult=self::calculate_exam_result($examId,$answers);
            static::insert("exam_results",[
               'user_id'=>$userId,
                'exam_id'=>$examId,
                'correct_answers'=>$examResult['correct_answers'],
                'incorrect_answers'=>$examResult['incorrect_answers'],
                'negative_mark'=>$examResult['negative_mark'],
                'final_mark'=>$examResult['final_mark']
            ]);
            return $examResult;
        }catch (\Exception $e){
            return false;
        }
    }

    protected static function getCurrentDatePersian()
    {
        $now = new \DateTime("NOW");
        $year = $now->format("Y");
        $month = $now->format("m");
        $day = $now->format("d");
        $time = $now->format("H:i");
        $persianDate = gregorian_to_jalali($year, $month, $day);
        return $persianDate[0] . "/" . $persianDate[1] . "/" . $persianDate[2] . " " . $time;
    }

    private static function calculate_exam_result($examId,$answers)
    {
        try{
            $examQuestions=self::get_questions_by_exam_id($examId);
            $examInfo=self::by_id($examId);
            $i=0;
            $finalMark=0;
            $correctAnswers=0;
            $incorrectAnswers=0;
            foreach($answers as $question=>$answer){
                if ($examQuestions[$i]['answer'] == $answer){
                    $finalMark+=$examQuestions[$i]['mark'];
                    $correctAnswers++;
                }else{
                    $incorrectAnswers++;
                }
                $i++;
            }
            $negativeMark=floor($incorrectAnswers/$examInfo['negative_mark_count']);
            $finalMark=$finalMark-$negativeMark;
            return [
              'correct_answers'=>$correctAnswers,
               'incorrect_answers'=>$incorrectAnswers,
               'final_mark'=>$finalMark,
                'negative_mark'=>$negativeMark,
                'exam_title'=>$examInfo['name'],
                'exam_id'=>$examId
            ];
        }catch (\Exception $e){
            return false;
        }
    }
}