@extends('Core::Admin.layouts.app')
@section('title', 'Cây thành viên')
@section('page-title', 'Cây thành viên')
@section('content')

    <style>
        a { color:#fff !important; text-decoration:none; }
        #chart-container { font-family:Arial; border:2px dashed #aaa; border-radius:5px; overflow:auto; text-align:center; min-height:400px; background:#fff; padding:16px; }
        .orgchart { background:#fff; }
        .orgchart td.left,.orgchart td.right,.orgchart td.top { border-color:#aaa; }
        .orgchart td>.down { background-color:#aaa; }
        .orgchart .node .title { height:40px !important; }

        /* Màu theo trạng thái */
        .orgchart .level-root .title   { background-color:#1e40af !important; }
        .orgchart .level-root .content { border-color:#1e40af !important; }
        .orgchart .level-10 .title     { background-color:#db2777 !important; }
        .orgchart .level-10 .content   { border-color:#db2777 !important; }
        .orgchart .level-5 .title      { background-color:#ea580c !important; }
        .orgchart .level-5 .content    { border-color:#ea580c !important; }
        .orgchart .level-3 .title      { background-color:#16a34a !important; }
        .orgchart .level-3 .content    { border-color:#16a34a !important; }
        .orgchart .level-0 .title      { background-color:#0000FF !important; }
        .orgchart .level-0 .content    { border-color:#dc2626 !important; }

        /* Chú thích */
        .legend-item { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; }
        .legend-dot  { width:16px; height:16px; border-radius:4px; flex-shrink:0; }
    </style>

    {{-- Chú thích --}}
    <div class="card" style="margin-bottom:16px; border:none; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1)">
        <div class="card-body" style="padding:15px 20px; display:flex; flex-wrap:wrap; gap:20px; align-items:center">
            <strong style="font-size:14px; color:#1e293b"><i class="fas fa-info-circle"></i> Chú thích:</strong>
            <div class="legend-item"><div class="legend-dot" style="background:#1e40af"></div>Root (bạn)</div>
            <div class="legend-item"><div class="legend-dot" style="background:#16a34a"></div>&ge; 3 F1 (Mở ví)</div>
            <div class="legend-item"><div class="legend-dot" style="background:#ea580c"></div>&ge; 5 F1</div>
            <div class="legend-item"><div class="legend-dot" style="background:#db2777"></div>&ge; 10 F1</div>
            <div class="legend-item"><div class="legend-dot" style="background:#0000FF"></div>&lt; 3 F1 (Chưa mở ví)</div>
        </div>
    </div>

    <div class="card" style="border:none; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1)">
        <div class="card-header" style="display:flex; align-items:center; gap:10px; background:#fff; border-bottom:1px solid #f1f5f9">
            <h5 style="margin:0; color:#1e293b; font-weight:700"><i class="fas fa-sitemap"></i> Cây tổ chức — {{ $user->name }}</h5>
            <span style="font-size:12px; color:#64748b; background:#f1f5f9; padding:2px 8px; border-radius:4px">Số ngày = Từ lần mua hàng cuối</span>
        </div>
        <div class="card-body">
            <div id="chart-container"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.0/css/jquery.orgchart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.0/js/jquery.orgchart.min.js"></script>

    <script>
        (function($){
            $(function(){
                var datasource = {!! json_encode($treeData) !!};
                $('#chart-container').orgchart({
                    'data'        : datasource,
                    'nodeContent' : 'title',
                    'createNode'  : function($node, data){
                        // Hiển thị ngày mua hàng rõ nét hơn
                        $node.append('<div style="font-size:12px; font-weight:700; color:#1e293b; margin-top:5px; border-top:1px solid #eee; padding-top:3px">' + data.date + '</div>');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
