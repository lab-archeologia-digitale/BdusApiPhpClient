function bdusApiClient(opts){

  var apiUrl = opts.url,
      app = opts.app,
      paginate = opts.paginate,
      $this = this,
      records_per_page;

  this.setRecordsPerPage = function(records_p_p){
    records_per_page = records_p_p;
  };

  this.getOne = function(tb, id, callback){
    getData(getUrl(tb) + 'id=' + id, false, callback);
  };

  this.searchAll = function(tb, callback){
    getData(getUrl(tb) + 'type=all', {}, callback);
	};

	this.searchString = function(tb, string, callback){
    getData(getUrl(tb) + 'type=fast&string=' + string, false, callback);
	};

	this.searchAdv = function(tb, form_data, callback){
		getData(getUrl(tb) + 'type=advanced&', form_data, callback);
	};

  this.searchSQL = function(tb, sql, callback){
    getData(getUrl(tb) + 'type=sqlExpert', {querytext: encodeURI(sql)}, callback);
  };

  this.go2page = function(tb, page, q_encoded, callback){
    getData(getUrl(tb) + 'type=encoded&page=' + page, {'q_encoded': q_encoded}, callback);
	};

  function getData(url, postdata, callback){
    var ajaxOpts = {
    	url: url,
      type: 'POST',
      data: postdata
    };

    $.ajax(ajaxOpts)
    .always(function(data){
      if (typeof callback === 'function'){
        callback(data, $this);
      }
    });
  }

  function getUrl(tb){
    return apiUrl + app + '/' + tb + '/' +
      (records_per_page ? 'records_per_page=' + records_per_page + '&' : '');
  }
}
