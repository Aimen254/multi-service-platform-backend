<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mail</title>
    <style>
        .main-div{
            /* border: 1px solid #eee; */
            padding: 20px !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .text-center{
            text-align: center !important;
        }
        .text-left{
            text-align: left !important;
        }
        .text-right{
            text-align: right !important;
        }
        .fs-td{
            font-size: 1.5vw !important;
        }
        .fs-14{
            font-size: 1.5vw !important;
        }
        .fs-14-bold{
            font-size: 1.5vw !important;
            font-weight: bold !important;
        }
        .fs-16-bold{
            font-size: 1.5vw !important;
            font-weight: bold !important;
        }
        img{
            height: 8vw !important;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        tbody{
            border: 1px solid #ddd;
        }
        th, td {
            padding: 20px 20px;
            text-align: left;
        }
        .p-0 { padding: 0px; }
        .p-1 { padding: 1px; }
        .p-2 { padding: 2px; }
        .p-3 { padding: 3px; }
        .p-4 { padding: 4px; }
        .p-5 { padding: 5px; }
        .p-10 { padding: 10px; }
        .p-15 { padding: 15px; }
        .p-20 { padding: 20px; }
        .p-25 { padding: 25px; }
        .p-30 { padding: 30px; }
        .p-40 { padding: 40px; }
        .p-50 { padding: 50px; }
        
        .px-0 { padding-left: 0px; padding-right: 0px; }
        .px-1 { padding-left: 1px; padding-right: 1px; }
        .px-2 { padding-left: 2px; padding-right: 2px; }
        .px-3 { padding-left: 3px; padding-right: 3px; }
        .px-4 { padding-left: 4px; padding-right: 4px; }
        .px-5 { padding-left: 5px; padding-right: 5px; }
        .px-10 { padding-left: 10px; padding-right: 10px; }
        .px-15 { padding-left: 15px; padding-right: 15px; }
        .px-20 { padding-left: 20px; padding-right: 20px; }
        .px-25 { padding-left: 25px; padding-right: 25px; }
        .px-30 { padding-left: 30px; padding-right: 30px; }
        .px-40 { padding-left: 40px; padding-right: 40px; }
        .px-50 { padding-left: 50px; padding-right: 50px; }
        
        .py-0 { padding-top: 0px; padding-bottom: 0px; }
        .py-1 { padding-top: 1px; padding-bottom: 1px; }
        .py-2 { padding-top: 2px; padding-bottom: 2px; }
        .py-3 { padding-top: 3px; padding-bottom: 3px; }
        .py-4 { padding-top: 4px; padding-bottom: 4px; }
        .py-5 { padding-top: 5px; padding-bottom: 5px; }
        .py-10 { padding-top: 10px; padding-bottom: 10px; }
        .py-15 { padding-top: 15px; padding-bottom: 15px; }
        .py-20 { padding-top: 20px; padding-bottom: 20px; }
        .py-25 { padding-top: 25px; padding-bottom: 25px; }
        .py-30 { padding-top: 30px; padding-bottom: 30px; }
        .py-40 { padding-top: 40px; padding-bottom: 40px; }
        .py-50 { padding-top: 50px; padding-bottom: 50px; }
        
        .pt-0 { padding-top: 0px; }
        .pt-1 { padding-top: 1px; }
        .pt-2 { padding-top: 2px; }
        .pt-3 { padding-top: 3px; }
        .pt-4 { padding-top: 4px; }
        .pt-5 { padding-top: 5px; }
        .pt-10 { padding-top: 10px; }
        .pt-15 { padding-top: 15px; }
        .pt-20 { padding-top: 20px; }
        .pt-25 { padding-top: 25px; }
        
        .m-0 { margin: 0px; }
        .m-1 { margin: 1px; }
        .m-2 { margin: 2px; }
        .m-3 { margin: 3px; }
        .m-4 { margin: 4px; }
        .m-5 { margin: 5px; }
        .m-10 { margin: 10px; }
        .m-15 { margin: 15px; }
        .m-20 { margin: 20px; }
        .m-25 { margin: 25px; }
        .m-30 { margin: 30px; }
        .m-40 { margin: 40px; }
        .m-50 { margin: 50px; }
        
        .mx-0 { margin-left: 0px; margin-right: 0px; }
        .mx-1 { margin-left: 1px; margin-right: 1px; }
        .mx-2 { margin-left: 2px; margin-right: 2px; }
        .mx-3 { margin-left: 3px; margin-right: 3px; }
        .mx-4 { margin-left: 4px; margin-right: 4px; }
        .mx-5 { margin-left: 5px; margin-right: 5px; }
        .mx-10 { margin-left: 10px; margin-right: 10px; }
        .mx-15 { margin-left: 15px; margin-right: 15px; }
        .mx-20 { margin-left: 20px; margin-right: 20px; }
        .mx-25 { margin-left: 25px; margin-right: 25px; }
        .mx-30 { margin-left: 30px; margin-right: 30px; }
        .mx-40 { margin-left: 40px; margin-right: 40px; }
        .mx-50 { margin-left: 50px; margin-right: 50px; }
        
        .my-0 { margin-top: 0px; margin-bottom: 0px; }
        .my-1 { margin-top: 1px; margin-bottom: 1px; }
        .my-2 { margin-top: 2px; margin-bottom: 2px; }
        .my-3 { margin-top: 3px; margin-bottom: 3px; }
        .my-4 { margin-top: 4px; margin-bottom: 4px; }
        .my-5 { margin-top: 5px; margin-bottom: 5px; }
        .my-10 { margin-top: 10px; margin-bottom: 10px; }
        .my-15 { margin-top: 15px; margin-bottom: 15px; }
        .my-20 { margin-top: 20px; margin-bottom: 20px; }
        .my-25 { margin-top: 25px; margin-bottom: 25px; }
        .my-30 { margin-top: 30px; margin-bottom: 30px; }
        .my-40 { margin-top: 40px; margin-bottom: 40px; }
        .my-50 { margin-top: 50px; margin-bottom: 50px; }
        
        .mt-0 { margin-top: 0px; }
        .mt-1 { margin-top: 1px; }
        .mt-2 { margin-top: 2px; }
        .mt-3 { margin-top: 3px; }
        .mt-4 { margin-top: 4px; }
        .mt-5 { margin-top: 5px; }
        .mt-10 { margin-top: 10px; }
        .mt-15 { margin-top: 15px; }
        .mt-20 { margin-top: 20px; }
        
        .text-primary { color: #007bff; }
        .text-secondary { color: #d2d4d6; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-info { color: #17a2b8; }
        .text-light { color: #f8f9fa; }
        .text-dark { color: #343a40; }
        
        .bg-primary { background-color: #007bff; }
        .bg-secondary { background-color: #d2d4d6; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; }
        .bg-info { background-color: #17a2b8; }
        .bg-light { background-color: #f8f9fa; }
        .bg-dark { background-color: #343a40; }
        </style>
</head>
    <body>
        <div class="main-div">
            @include('includes.mails.header')
            @yield('content')
        </div>
    </body>
</html>
