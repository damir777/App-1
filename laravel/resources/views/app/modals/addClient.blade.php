<div class="modal inmodal" id="addClient" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceIn" id="tour-seven-step">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('main.new_client') }}</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url' => '#', 'autocomplete' => 'off', 'class' => 'client-form')) }}
                {{ Form::hidden('document_insert', 'T', array('class' => 'document-insert')) }}
                {{ Form::hidden('retail_client', 'F', array('class' => 'retail-client')) }}
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ trans('main.client_type') }}</label>
                                    {{ Form::select('client_type', array(1 => trans('main.private'),
                                        2 => trans('main.company')), 1, array('class' => 'form-control client-type')) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ trans('main.int_client') }}</label>
                                    {{ Form::select('int_client', array('F' => trans('main.no'),
                                        'T' => trans('main.yes')), 'F', array('class' => 'form-control int-client')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.city') }}</label>
                            {{ Form::text('city', null, array('class' => 'form-control city')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.name') }}</label>
                            {{ Form::text('name', null, array('class' => 'form-control client-name')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.country') }}</label>
                            {{ Form::select('country', $countries, 'HR', array('class' => 'form-control country')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div id="oib-div">
                            <div class="form-group">
                                <label>{{ trans('main.oib') }} <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_oib') }}"></i></label>
                                {{ Form::text('oib', null, array('class' => 'form-control oib')) }}
                            </div>
                        </div>
                        <div id="tax-number-div" style="display: none">
                            <div class="form-group">
                                <label>{{ trans('main.tax_number') }}</label>
                                {{ Form::text('tax_number', null, array('class' => 'form-control tax-number')) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.zip_code') }}</label>
                            <div id="zip-code-select-div">
                                {{ Form::select('zip_code_select', $zip_codes, null,
                                    array('class' => 'form-control zip-code-select')) }}
                            </div>
                            <div id="zip-code-text-div" style="display: none">
                                {{ Form::text('zip_code_text', null, array('class' => 'form-control zip-code')) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.address') }}</label>
                            {{ Form::text('address', null, array('class' => 'form-control address')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.email') }}</label>
                            {{ Form::text('email', null, array('class' => 'form-control email')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.phone') }}</label>
                            {{ Form::text('phone', null, array('class' => 'form-control phone')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.rebate') }}</label>
                            {{ Form::text('rebate', null, array('class' => 'form-control rebate')) }}
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">{{ trans('main.cancel') }}</button>
                <button type="button" class="btn btn-primary insert-client">{{ trans('main.save') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    var no_results_trans = '{{ trans('main.no_results') }}';
    var client_insert = '{{ trans('main.client_insert') }}';
</script>

{{ HTML::script('js/functions/clients.js?v='.date('YmdHi')) }}