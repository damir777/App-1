<div class="modal inmodal" id="notesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceIn">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('main.notes') }}</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url' => '#', 'autocomplete' => 'off', 'class' => 'custom-search-form')) }}
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>{{ trans('main.note') }}</label>
                            <div class="input-group">
                                {{ Form::select('notes', $notes, null, array('class' => 'form-control note-select')) }}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-success add-note">{{ trans('main.add') }}</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row m-t">
                    <div id="notes-form">
                        @if (isset($document_notes))
                            @foreach ($document_notes as $note)
                                <div class="note-form-element">
                                    <div class="col-sm-10">
                                        <div class="form-group">
                                            {{ Form::textarea('custom_note', $note->note, array('class' => 'form-control note-text',
                                                'rows' => 2, 'data-note-id' => $note->id )) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger btn-xs remove-note">
                                                {{ trans('main.delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('main.ok') }}</button>
            </div>
        </div>
    </div>
</div>