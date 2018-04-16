$('#reservation_start').datetimepicker({
    format: 'yyyy-mm-d HH:MM',
    uiLibrary: 'bootstrap4',
    modal: true,
    datepicker: {
        minDate: function() {
            var date = new Date();
            date.setDate(date.getDate()-1);
            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
        }
    }
});

$('#reservation_end').datetimepicker({
    format: 'yyyy-mm-d HH:MM',
    uiLibrary: 'bootstrap4',
    modal: true,
    datepicker: {
        minDate: function() {
            var date = new Date();
            date.setDate(date.getDate()-1);
            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
        }
    }
});