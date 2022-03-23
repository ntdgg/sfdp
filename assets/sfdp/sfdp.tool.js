var sfdpExcel = {
    loadExcel:function (obj) {
        document.getElementById(obj).click();
        document.getElementById(obj).addEventListener('change', function(e) {
            var files = e.target.files;
            if(files.length == 0) return;
            var f = files[0];
            if(!/\.xlsx$/g.test(f.name)) {
                alert('仅支持读取xlsx格式！');
                return;
            }
            sfdpExcel.readExcelFile(f, function(workbook) {
                sfdpExcel.readWorkbook(workbook);
            });
        });
    },
    readExcelFile:function (file, callback) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var data = e.target.result;
            var workbook = XLSX.read(data, {type: 'binary'});
            if(callback) callback(workbook);
        };
        reader.readAsBinaryString(file);
    },
    readWorkbook:function (workbook) {
        var sheetNames = workbook.SheetNames;
        var worksheet = workbook.Sheets[sheetNames[0]]; //读取第一张sheet
        var csv = XLSX.utils.sheet_to_csv(worksheet);
        var html = '<table class="layui-table">';
        var rows = csv.split('\n');
        rows.pop(); //
        rows.forEach(function(row, idx) {
            var columns = row.split(',');
            columns.unshift(idx+1);
            if(idx == 0) {
                html += '<tr>';
                for(var i=0; i<columns.length; i++) {
                    html += '<th>' + (i==0?'':String.fromCharCode(65+i-1)) + '</th>';
                }
                html += '</tr>';
            }
            html += '<tr>';
            columns.forEach(function(column) {
                html += '<td>'+column+'</td>';
            });
            html += '</tr>';
        });
        html += '</table>';
        document.getElementById('result').innerHTML = html;
    },
    ctrlExcel:function (type='down') {
        if(type=='save'){
            var sheet=(sfdpExcel.csvData()).csv;
                if(sheet.length==0){
                    layer.msg(`没有加载模板数据！`);return;
                }
            sfdp.sAjax(server_url,{csv:sheet,table:db_table})
        }
        if(type=='Tpl'){
            var sheet=(sfdpExcel.csvData(type,tpl_csv)).sheet;
            var workbook = {
                SheetNames: ['sheet1'],
                Sheets: {}
            };
            workbook.Sheets['sheet1'] = sheet;
            // 生成excel的配置项
            var wopts = {
                bookType: 'xlsx', // 要生成的文件类型
                bookSST: false, // 是否生成Shared String Table，官方解释是，如果开启生成速度会下降，但在低版本IOS设备上有更好的兼容性
                type: 'binary'
            };
            var wbout = XLSX.write(workbook, wopts);
            var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
            // 字符串转ArrayBuffer
            function s2ab(s) {
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }
            sfdpExcel.downFile(blob, '导出.xlsx');
        }
        if(type=='down'){
            var sheet=(sfdpExcel.csvData()).sheet;
            if(Object.keys(sheet).length==0){
                layer.msg(`没有加载模板数据！`);return;
            }
            var workbook = {
                SheetNames: ['sheet1'],
                Sheets: {}
            };
            workbook.Sheets['sheet1'] = sheet;
            // 生成excel的配置项
            var wopts = {
                bookType: 'xlsx', // 要生成的文件类型
                bookSST: false, // 是否生成Shared String Table，官方解释是，如果开启生成速度会下降，但在低版本IOS设备上有更好的兼容性
                type: 'binary'
            };
            var wbout = XLSX.write(workbook, wopts);
            var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
            // 字符串转ArrayBuffer
            function s2ab(s) {
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }
            sfdpExcel.downFile(blob);
        }
    },
    csvData:function (type,tpl_csv='') {
        var csv = [];
        $($('#result table')[0]).find('tr').each(function() {
            var temp = [];
            $(this).find('td').each(function() {
                temp.push($(this).html());
            })
            temp.shift(); // 移除第一个
            csv.push(temp.join(','));
        });
        csv.shift();
        csv.join('\n');
        if(type=='Tpl'){
            delete csv;
            var csv = [];
                csv.push(tpl_csv);
                csv.push(',,');
        }
        var sheet = {}; // 将要生成的sheet
        csv.forEach(function(row, i) {
            row = row.split(',');
            if(i == 0) sheet['!ref'] = 'A1:'+String.fromCharCode(65+row.length-1)+(csv.length-1);
            row.forEach(function(col, j) {
                sheet[String.fromCharCode(65+j)+(i+1)] = {v: col};
            });
        });
        return {sheet,csv};
    },
    downFile:function (url) {
        if(typeof url == 'object' && url instanceof Blob)
        {
            url = URL.createObjectURL(url); // 创建blob地址
        }
        var aLink = document.createElement('a');
        aLink.href = url;
        aLink.download = tpl_name || ''; // HTML5新增的属性，指定保存文件名，可以不要后缀，注意，file:///模式下不会生效
        var event;
        if(window.MouseEvent) event = new MouseEvent('click');
        else
        {
            event = document.createEvent('MouseEvents');
            event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
        }
        aLink.dispatchEvent(event);
    }
}