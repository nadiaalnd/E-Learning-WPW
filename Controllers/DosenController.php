<?php
require_once 'BaseController.php';

class DosenController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $isDosen = session()->get('user')['role'] == 'dosen' && $this->isAuth();
        if (!$isDosen) $this->redirect('/dashboard');
    }

    public function manage_matkul_index()
    {
        $this->loadModel('Matkul');
        $data = $this->model->getMatkulWhere('instructor_id', session()->get('user')['id']);
        // dd(session()->get('user'));
        echo view()->render('main/dosen/manage-matkul', ['data' => $data]);
    }

    public function manage_matkul_materi($id)
    {
        $this->loadModel('Materi');
        $data = $this->model->getMateriWhere('course_id', $id);
        $this->loadModel('Matkul');
        $matkul = $this->model->getMatkul('id', $id);
        $matkul = $matkul['course_name'];
        echo view()->render('main/dosen/manage-materi', ['data' => $data, 'id' => $id, 'matkul' => $matkul]);
    }

    public function manage_matkul_materi_add()
    {
        $fileName = fileHandle()->upload($this->req_file('file'), BASE_PATH . '/assets/uploads/materi/');
        $data = [
            'course_id' => $this->req_post('matkul'),
            'lesson_name' => $this->req_post('nama'),
            'attachment' => $fileName,
        ];

        $this->loadModel('Materi');
        $this->model->addMateri($data);
        echo "<script>alert('Materi berhasil ditambahkan');window.location.href='/dosen/matkul/materi/" . $this->req_post('matkul') . "';</script>";
    }

    public function manage_matkul_materi_edit($id)
    {
        $this->loadModel('Materi');
        $model = $this->model->getMateri('id', $id);
        $fileName = fileHandle()->upload($this->req_file('file'), BASE_PATH . '/assets/uploads/materi/');
        $data = [
            'course_id' => $this->req_post('matkul'),
            'lesson_name' => $this->req_post('nama'),
            'attachment' => $fileName ? $fileName : $model['attachment'],
        ];

        $this->model->editMateri($data, $id);
        echo "<script>alert('Materi berhasil ubah');window.location.href='/dosen/matkul/materi/" . $this->req_post('matkul') . "';</script>";
    }

    public function manage_matkul_materi_download($path)
    {
        $this->loadModel('Materi');
        $data = $this->model->getMateri('id', $path);
        $path = $data['attachment'];
        fileHandle()->download(BASE_PATH . '/assets/uploads/materi/' . $path);
    }

    public function manage_matkul_materi_delete($id)
    {
        $this->loadModel('Materi');
        $d = $this->model->deleteMateri($id);
        echo "<script>alert('Materi berhasil dihapus');window.location.href='/dosen/matkul/materi/'.$id;</script>";
    }

    public function manage_matkul_tugas($id)
    {
        $this->loadModel('Assignment');
        $data = $this->model->getAllTugasWithMatkul('course_id', $id);
        $this->loadModel('Matkul');
        $matkul = $this->model->getMatkul('id', $id);
        $matkul = $matkul['course_name'];
        echo view()->render('main/dosen/manage-tugas', ['data' => $data, 'id' => $id, 'matkul' => $matkul]);
    }

    public function manage_matkul_tugas_add()
    {
        $data = [
            'assignment_name' => $this->req_post('nama'),
            'course_id' => $this->req_post('matkul'),
            'deadline' => $this->req_post('deadline'),
        ];
        $this->loadModel('Assignment');
        $this->model->addTugas($data);
        echo "<script>alert('Tugas berhasil ditambahkan');window.location.href='/dosen/matkul/tugas/" . $this->req_post('matkul') . "';</script>";
    }
}
