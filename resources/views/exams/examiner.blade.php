<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam</title>
    <link rel="stylesheet" href="/css/app.css">
    <!-- React and ReactDOM CDN -->
    <script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>
    <!-- TypeScript Compiler -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typescript/4.8.4/typescript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.8/axios.min.js" integrity="sha512-v8+bPcpk4Sj7CKB11+gK/FnsbgQ15jTwZamnBf/xDmiQDcgOIYufBo6Acu1y30vrk8gg5su4x0CG3zfPaq5Fcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
    <div id="exam-root"></div>

    <script>
        // Pass data to the React component
        window.examData = @json($examData);
    </script>

    <!-- Load your TypeScript file -->
    <script type="module" src="/js/main.tsx"></script>
</body>
</html>
