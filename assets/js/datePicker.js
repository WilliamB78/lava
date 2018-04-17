$('#reservation_date').datepicker({
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