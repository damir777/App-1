<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Note;
use App\Repositories\NoteRepository;

class NoteController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new NoteRepository;
    }


    //get notes
    public function getNotes()
    {
        //call getNotes method from NoteRepository to get notes
        $notes = $this->repo->getNotes();

        //if response status = '0' show error page
        if ($notes['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.notes.list', ['notes' => $notes['data']]);
    }

    //add note
    public function addNote()
    {
        return view('app.notes.addNote');
    }

    //insert note
    public function insertNote(Request $request)
    {
        $text = $request->text;

        //validate form inputs
        $validator = Validator::make($request->all(), Note::validateNoteForm());

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('AddNote')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call insertNote method from NoteRepository to insert note
        $response = $this->repo->insertNote($text);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('AddNote')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetNotes')->with('success_message', trans('main.note_insert'));
    }

    //edit note
    public function editNote($id)
    {
        //call getNoteDetails method from NoteRepository to get note details
        $note = $this->repo->getNoteDetails($id);

        //if response status = '0' return error message
        if ($note['status'] == 0)
        {
            return redirect()->route('GetNotes')->with('error_message', trans('errors.error'));
        }

        return view('app.notes.editNote', ['note' => $note['data']]);
    }

    //update note
    public function updateNote(Request $request)
    {
        $id = $request->id;
        $text = $request->text;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Note::validateNoteForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditNote', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateNote method from NoteRepository to update note
        $response = $this->repo->updateNote($id, $text);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditNote', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetNotes')->with('success_message', trans('main.note_update'));
    }

    //delete note
    public function deleteNote($id)
    {
        //call deleteNote method from NoteRepository to delete note
        $response = $this->repo->deleteNote($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetNotes')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetNotes')->with('success_message', trans('main.note_delete'));
    }
}
