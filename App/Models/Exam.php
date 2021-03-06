<?php
namespace App\Models;

use Core\Model;
use \PDO;

class Exam extends Model
{
    public static function all($userId=null)
    {
        try{
            $db=static::getDB();
            $sql="SELECT exams.id,exams.study_field_id,exams.name,exams.is_disabled,exams.question_count,exams.has_negative_mark,exams.negative_mark_count,exams.exam_time,exams.exam_mark,exams.exam_coefficient,exams.create_date_persian,study_fields.title AS study_field FROM exams INNER JOIN study_fields ON study_fields.id = exams.study_field_id";
            if ($userId){
                $sql.=" WHERE exams.id NOT IN (SELECT exam_id FROM exam_results WHERE user_id = '$userId')";
            }
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

    public static function get_completed_exams_by_user_id($userId)
    {
        try{
            $db=static::getDB();
            $sql="SELECT exam_results.final_mark AS user_final_mark,exam_results.exam_id,exams.name,exams.question_count,exams.exam_mark FROM exam_results INNER JOIN exams ON exams.id = exam_results.exam_id WHERE exam_results.user_id = :user_id";
            $stmt=$db->prepare($sql);
            $stmt->bindParam(":user_id",$userId);
            return $stmt->execute() ? $stmt->fetchAll(PDO::FETCH_ASSOC):[];
        }catch (\Exception $e){
            return [];
        }
    }

    public static function get_results_info_by_id($examId)
    {
        try{
            $db=static::getDB();
            $sql="SELECT exam_results.exam_id,exam_results.correct_answers,exam_results.incorrect_answers,exam_results.negative_mark,exam_results.final_mark,study_fields.title AS study_field,exams.name,exams.question_count,exams.has_negative_mark,exams.negative_mark_count,exams.exam_time,exams.exam_mark,exams.create_date_persian,exams.is_disabled FROM `exam_results`INNER JOIN exams ON exams.id = exam_results.exam_id INNER JOIN study_fields ON exams.study_field_id = study_fields.id WHERE exam_results.exam_id = :exam_id";
            $stmt=$db->prepare($sql);
            $stmt->bindParam(":exam_id",$examId);
            return $stmt->execute() ? $stmt->fetch(PDO::FETCH_ASSOC):false;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function new($post)
    {
        try{
            $post['create_date_persian']=self::getCurrentDatePersian();
            static::insert("exams",$post);
            return static::get_last_inserted_id();
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

    public static function get_all_questions_by_id($examId)
    {
        try{
            return static::select("questions","*",['exam_id'=>$examId]);
        }catch (\Exception $e){
            return false;
        }
    }

    public static function delete_by_id($exam_id)
    {
        try{
            static::delete("exams",['id'=>$exam_id]);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function get_all_exams_results()
    {
        try{
            $db=static::getDB();
            $sql="SELECT exam_results.id,exam_results.user_id,exam_results.exam_id,exam_results.correct_answers,exam_results.incorrect_answers,exam_results.negative_mark,exam_results.final_mark,exams.name,exams.question_count,exams.has_negative_mark,exams.negative_mark_count,exams.exam_time,exams.exam_mark,exams.exam_coefficient,exams.create_date_persian,exams.is_disabled,study_fields.title AS study_field,users.fname,users.lname,users.username FROM exam_results INNER JOIN exams ON exams.id = exam_results.exam_id INNER JOIN study_fields ON study_fields.id = exams.study_field_id INNER JOIN users ON users.id = exam_results.user_id";
            $result=$db->query($sql);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC):[];
        }catch (\Exception $e){
            return [];
        }
    }
    public static function exam_result_by_id($examId)
    {
        try{
            $db=static::getDB();
            $sql="SELECT exam_results.id,exam_results.user_id,exam_results.exam_id,exam_results.correct_answers,exam_results.incorrect_answers,exam_results.negative_mark,exam_results.final_mark,exams.name,exams.question_count,exams.has_negative_mark,exams.negative_mark_count,exams.exam_time,exams.exam_mark,exams.exam_coefficient,exams.create_date_persian,exams.is_disabled,study_fields.title AS study_field,users.fname,users.lname,users.username FROM exam_results INNER JOIN exams ON exams.id = exam_results.exam_id INNER JOIN study_fields ON study_fields.id = exams.study_field_id INNER JOIN users ON users.id = exam_results.user_id WHERE exam_results.exam_id = :exam_id";
            $stmt=$db->prepare($sql);
            $stmt->bindParam(":exam_id",$examId);
            return $stmt->execute() ? $stmt->fetch(PDO::FETCH_ASSOC):[];
        }catch (\Exception $e){
            return [];
        }
    }

    public static function get_user_question_answers_by_exam_id($examId,$userId)
    {
        try{
            $db=static::getDB();
            $sql="SELECT exam_logs.choice,questions.title,questions.option1,questions.option2,questions.option3,questions.option4,questions.answer FROM exam_results INNER JOIN questions ON questions.exam_id = exam_results.exam_id INNER JOIN exam_logs ON questions.id = exam_logs.question_id WHERE exam_results.exam_id = :exam_id AND exam_logs.user_id = :user_id";
            $stmt=$db->prepare($sql);
            $stmt->bindParam(":exam_id",$examId);
            $stmt->bindParam(":user_id",$userId);
            return $stmt->execute() ? $stmt->fetchAll(PDO::FETCH_ASSOC):[];
        }catch (\Exception $e){
            return [];
        }
    }
}