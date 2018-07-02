<div class="modal inmodal" id="updateLicence" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceIn">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('main.edit_licence') }}</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url' => '#', 'autocomplete' => 'off')) }}
                {{ Form::hidden('company_id', null, array('class' => 'company-id')) }}
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3 text-center">
                        <div class="form-group">
                            <label>{{ trans('main.licence_end') }}</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {{ Form::text('licence_end', null, array('class' => 'form-control licence-end')) }}
                            </div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">{{ trans('main.cancel') }}</button>
                <button type="button" class="btn btn-primary update-licence">{{ trans('main.save') }}</button>
            </div>
        </div>
    </div>
</div>

{{ HTML::script('js/functions/licence.js?v='.date('YmdHi')) }}