<?php
require_once 'Model.php';

class Tugas extends Model
{
    public function getTugas($column, $val)
    {
        return  $this->getBy('submissions', $column, $val);
    }

    public function getAllTugas()
    {
        return $this->getAll('submissions');
    }

    public function addTugas($data)
    {
        return $this->create('submissions', $data);
    }

    public function editTugas($data, $id)
    {
        return $this->update('submissions', $id, $data);
    }

    public function deleteTugas($id)
    {
        return $this->delete('submissions', $id);
    }

    public function getAllTugasWithMatkul()
    {
        $sql = "SELECT submissions.*, courses.course_name AS matkul FROM submissions JOIN courses ON submissions.course_id = courses.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrCreate($data)
    {
        $sql = "SELECT * FROM submissions WHERE user_id = ? AND assignment_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$data['user_id'], $data['assignment_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $sql = "UPDATE submissions SET file = ?, submission_time = ? WHERE user_id = ? AND assignment_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['file'], $data['submission_time'], $data['user_id'], $data['assignment_id']]);
        } else {
            $sql = "INSERT INTO submissions (user_id, file, submission_time, assignment_id) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['user_id'], $data['file'], $data['submission_time'], $data['assignment_id']]);
        }

        return $stmt->rowCount();
    }

    public function getAllTugasWithSubmission($id, $course_id)
    {
        //     $sql = "SELECT
        //     submissions.*,
        //     assignments.assignment_name AS tugas
        // FROM
        //     submissions
        // JOIN
        //     assignments ON submissions.assignment_id = assignments.id
        // WHERE
        //     submissions.user_id = ?";
        $sql = "SELECT submissions.*, assignments.assignment_name,assignments.deadline,assignments.id as Assignment_id FROM submissions JOIN assignments ON submissions.assignment_id = assignments.id WHERE submissions.user_id = ? AND assignments.course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $course_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
