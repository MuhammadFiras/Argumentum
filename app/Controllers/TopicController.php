<?php

namespace App\Controllers;

use App\Models\TopicModel;

class TopicController extends BaseController
{
  protected $topicModel;

  public function __construct()
  {
    $this->topicModel = new TopicModel();
  }

  public function addForm()
  {
    $data = [
      'title' => 'Add Topic'
    ];

    return view('admin/content/form/add_topic', $data);
  }

  public function editForm($topicId)
  {
    $currentTopic = $this->topicModel->where('id', $topicId)->first();

    if (!$currentTopic) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Topic dengan ID tersebut tidak ditemukan.');
    }

    $data = [
      'title' => 'Edit Topic',
      'topic' => $currentTopic
    ];

    return view('admin/content/form/edit_topic', $data);
  }

  public function insert()
  {
    $rules = [
      'newTopic' => [
        'rules' => 'required|max_length[30]|is_unique[topics.name]',
        'errors' => [
          'required' => 'Topic tidak boleh kosong.',
          'max_length' => 'Topic maksimal {param} karakter.',
          'is_unique' => 'Topic sudah ada.'
        ]
      ]
    ];

    if (!$this->validate($rules)) {
      return redirect()->to('/admin/form/add-topics')->withInput()->with('validation', $this->validator);
    }

    $newTopic = [
      'name' => $this->request->getPost('newTopic')
    ];

    if ($this->topicModel->insert($newTopic)) {
      return redirect()->to('/admin/tables/topics')->with('success', 'Data Topic berhasil ditambahkan.');
    } else {
      return redirect()->to('/admin/tables/topics')->with('error', 'Gagal menambahkan data Topic.');
    }
  }

  public function update($topicId)
  {
    $currentTopic = $this->topicModel->where('id', $topicId)->first();

    if (!$currentTopic) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Topic dengan ID tersebut tidak ditemukan.');
    }

    if ($currentTopic['name'] === $this->request->getPost('newTopic')) {
      $newTopicRules = 'required|max_length[30]';
    } else {
      $newTopicRules = 'required|max_length[30]|is_unique[topics.name]';
    }

    $rules = [
      'newTopic' => [
        'rules' => $newTopicRules,
        'errors' => [
          'required' => 'Topic tidak boleh kosong.',
          'max_length' => 'Topic maksimal {param} karakter.',
          'is_unique' => 'Topic sudah ada.'
        ]
      ]
    ];

    if (!$this->validate($rules)) {
      return redirect()->to('/admin/form/edit-topics/' . $currentTopic['id'])->withInput()->with('validation', $this->validator);
    }

    $newTopic = [
      'name' => $this->request->getPost('newTopic')
    ];

    if ($this->topicModel->update($topicId, $newTopic)) {
      return redirect()->to('/admin/tables/topics')->with('success', 'Data Topic berhasil diperbarui.');
    } else {
      return redirect()->to('/admin/tables/topics')->with('error', 'Gagal memperbarui data Topic.');
    }
  }

  public function delete($topicId)
  {
    if ($this->topicModel->delete($topicId)) {
      return redirect()->to('/admin/tables/topics')->with('success', 'Data Topic berhasil dihapus.');
    } else {
      return redirect()->to('/admin/tables/topics')->with('error', 'Gagal menghapus data Topic.');
    }
  }
}
