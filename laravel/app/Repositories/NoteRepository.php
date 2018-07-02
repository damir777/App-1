<?php

namespace App\Repositories;

use Exception;
use App\Note;

class NoteRepository extends UserRepository
{
    //get notes
    public function getNotes()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $notes = Note::select('id', 'text')->where('company_id', '=', $company_id)->paginate(30);

            return ['status' => 1, 'data' => $notes];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert note
    public function insertNote($text)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $note = new Note;
            $note->company_id = $company_id;
            $note->text = $text;
            $note->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get note details
    public function getNoteDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $note = Note::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if note doesn't exist return error status
            if (!$note)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $note];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update note
    public function updateNote($id, $text)
    {
        try
        {
            $note = Note::find($id);
            $note->text = $text;
            $note->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete note
    public function deleteNote($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $note = Note::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if note doesn't exist return error status
            if (!$note)
            {
                return ['status' => 0];
            }

            //delete note
            $note->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get notes - select
    public function getNotesSelect()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set notes array
            $notes_array = [];

            $notes = Note::select('id', 'text')->where('company_id', '=', $company_id)->get();

            //loop through all notes
            foreach ($notes as $note)
            {
                //add note to notes array
                $notes_array[$note->id] = $note->text;
            }

            return ['status' => 1, 'data' => $notes_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
