<html>
    <head>
        <script src="jquery-3.3.1.js"></script>
    </head>
    <body>
        <div>
            <button id="stop">stop!</button>
            <table>
                <thead>
                    <tr>
                        <th>heft</th>
                        <th>ct</th>
                        <th>sa</th>
                        <th>status</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
        
        <script>
            $(function(){
                var permission = 1;
                var total = {
                    equal : 0,
                    heft : 0,
                    ct : 0,
                    sa : 0,
                };
            
                getRandomOne();
                
                $("#stop").on("click",function(){
                    permission = 0;
                });
                
                function getRandomOne()
                {
                    jQuery.ajax({
                        url: "run.php",
                        success: function (result) {
                            result = JSON.parse(result);
                            best = "sa";
                            val = result.sa
                            
                            if(result.heft < val){
                                best = "heft";
                                val  = result.heft;
                            }
                            if(result.ct < val){
                                best = "ct";
                                val  = result.heft;
                            }
                            if(result.sa < val){
                                best = "sa";
                                val  = result.heft;
                            }
                            if(result.heft >= result.sa && result.sa == result.ct){
                                best = "equal";
                                val  = result.heft;
                            }
                            
                            total[best]++;
                            
                            content = "<tr><td>"+result.heft+"</td>"
                                     +"<td>"+result.ct+"</td>"
                                     +"<td>"+result.sa+"</td>"
                                     +"<td>"+best+"</td><tr>";
                            $("tbody").append(content);
                        },
                        error: function() {
                            console.error("runRequests Error");
                        },
                        complete: function() {
                            if(permission == 1){
                                getRandomOne();
                            }
                        }
                    });
                }
            });
        </script>
    </body>
</html>